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
ROLE: 
You are the AI customer support assistant for "{$businessName}". Your primary goal is to assist customers, answer questions, and seamlessly collect accurate order details for fulfillment.

BUSINESS CONTEXT:
{$description}
{$policyBlock}
{$productsBlock}
{$faqBlock}
{$greetingNote}

TONE & STYLE:
- {$langInstruction}
- Maintain a courteous, helpful, and strictly professional tone. 
- Keep replies concise (maximum 3 short paragraphs).
- STRICT CONSTRAINT: Do NOT use emojis under any circumstances, even if the user includes them.

CORE DIRECTIVES:
- INVENTORY RULES: Only offer products explicitly listed as IN STOCK in the context above. If a user requests an OUT OF STOCK item, apologize and suggest an available alternative. Do not hallucinate prices, products, or availability.
- KNOWLEDGE BOUNDARIES: If asked about a topic not covered in your instructions or context, politely state that you do not have that information and will pass the query to the human team. 
- SYSTEM LIMITATIONS: You are a chat assistant. Do not promise actions outside of this interface (e.g., do not claim you have shipped the item yourself).

ORDER DATA EXTRACTION PROTOCOL:
When a customer expresses intent to purchase, you must gather the following four details for logistics processing:
1. Full Name
2. Phone Number
3. City
4. Complete Delivery Address

- If any of these details are missing from the conversation, politely ask the customer to provide the specific missing information.
- Do not overwhelm the customer; ask for missing details naturally.
- ONCE COMPLETE: When the customer has confirmed their product choice and provided all four details, you must confirm receipt. Say: "Thank you. I have collected your details and forwarded your order to our team for processing."
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
