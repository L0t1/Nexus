<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Merchant;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    /**
     * Generate an AI auto-reply for an inbound customer message.
     * Returns the reply text, or null if the AI cannot produce one.
     */
    public function generateReply(Message $inboundMessage, Conversation $conversation): ?string
    {
        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            Log::warning('AiChatService: no OpenAI API key configured');
            return null;
        }

        $merchant = $conversation->merchant;

        if (!$merchant) {
            return null;
        }

        $systemPrompt = $this->buildSystemPrompt($merchant);
        $history      = $this->buildMessageHistory($conversation, $inboundMessage);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => config('services.openai.model', 'gpt-4o-mini'),
                    'messages'    => array_merge(
                        [['role' => 'system', 'content' => $systemPrompt]],
                        $history,
                    ),
                    'temperature' => 0.6,
                    'max_tokens'  => 300,
                ]);

            if (!$response->successful()) {
                Log::warning('AiChatService: OpenAI error', [
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);
                return null;
            }

            $reply = trim($response->json('choices.0.message.content') ?? '');

            return $reply !== '' ? $reply : null;
        } catch (\Exception $e) {
            Log::error('AiChatService: exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function buildSystemPrompt(Merchant $merchant): string
    {
        $info = $merchant->settings['ai_business_info'] ?? [];

        $businessName  = $merchant->business_name;
        $description   = $info['description']     ?? 'An online business.';
        $language      = $info['language']         ?? 'auto';
        $greeting      = $info['greeting']         ?? '';
        $returnPolicy  = $info['return_policy']    ?? '';
        $shippingInfo  = $info['shipping_info']    ?? '';
        $businessHours = $info['business_hours']   ?? '';
        $faqs          = $info['faqs']             ?? [];

        // ── Active products list ──────────────────────────────────────────────
        $products = $merchant->products()
            ->where('is_active', true)
            ->select(['name', 'sku', 'price', 'stock', 'description'])
            ->orderBy('name')
            ->get();

        $productLines = $products->map(function ($p) {
            $stock = $p->stock > 0 ? "in stock ({$p->stock})" : 'OUT OF STOCK';
            $desc  = $p->description ? " — {$p->description}" : '';
            return "- {$p->name} (SKU: {$p->sku}) | Price: {$p->price} | {$stock}{$desc}";
        })->join("\n");

        // ── FAQ block ─────────────────────────────────────────────────────────
        $faqBlock = '';
        if (!empty($faqs)) {
            $faqLines = collect($faqs)->map(fn ($faq) => "Q: {$faq['q']}\nA: {$faq['a']}")->join("\n\n");
            $faqBlock = "\n\nFREQUENTLY ASKED QUESTIONS:\n{$faqLines}";
        }

        // ── Language instruction ──────────────────────────────────────────────
        $langInstruction = match ($language) {
            'en' => 'Always respond in English.',
            'sq' => 'Always respond in Albanian (Shqip).',
            default => 'Detect the customer\'s language from their message and respond in the same language. Support Albanian and English.',
        };

        $policyBlock = '';
        if ($returnPolicy)  $policyBlock .= "\nReturn Policy: {$returnPolicy}";
        if ($shippingInfo)  $policyBlock .= "\nShipping: {$shippingInfo}";
        if ($businessHours) $policyBlock .= "\nBusiness Hours: {$businessHours}";

        $greetingNote = $greeting ? "\nDefault greeting: {$greeting}" : '';

        $productsBlock = $productLines
            ? "\n\nAVAILABLE PRODUCTS:\n{$productLines}"
            : "\n\nNo products are currently listed.";

        return <<<PROMPT
You are the AI customer support assistant for "{$businessName}".

ABOUT THE BUSINESS:
{$description}{$policyBlock}{$greetingNote}
{$productsBlock}{$faqBlock}

INSTRUCTIONS:
- {$langInstruction}
- Be friendly, concise, and helpful. Keep replies under 3 short paragraphs.
- STOCK: Never tell a customer a product is available if it shows OUT OF STOCK above. If they ask for an out-of-stock item, apologize and suggest alternatives if available.
- ORDER COLLECTION: When a customer wants to buy something, politely collect these details one step at a time if not already provided: (1) full name, (2) phone number, (3) city, (4) delivery address. Once all four are provided alongside a product, confirm the order has been received and is being processed.
- If asked about something you don't know, politely say you'll pass the question to the team.
- Never make up prices, availability, or policies that are not listed above.
- Do NOT include emojis unless the customer uses them first.
- You are only a chat assistant — do not promise actions you cannot perform (e.g. do not say you placed or confirmed the order yourself).
PROMPT;
    }

    /**
     * Build the conversation history array for the OpenAI messages payload.
     * We take the last 8 messages (excluding the current one) to give context.
     */
    private function buildMessageHistory(Conversation $conversation, Message $currentMessage): array
    {
        $previous = $conversation->messages()
            ->where('id', '!=', $currentMessage->id)
            ->whereNotNull('body')
            ->latest()
            ->take(8)
            ->get()
            ->reverse()
            ->values();

        $history = $previous->map(function (Message $msg) {
            return [
                'role'    => $msg->direction === 'inbound' ? 'user' : 'assistant',
                'content' => $msg->body,
            ];
        })->toArray();

        // Append the current inbound message
        $history[] = [
            'role'    => 'user',
            'content' => $currentMessage->body,
        ];

        return $history;
    }
}
