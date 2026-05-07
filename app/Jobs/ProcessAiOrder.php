<?php

namespace App\Jobs;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAiOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 5;

    public function __construct(
        public Message $message,
        public Conversation $conversation,
    ) {}

    public function handle(): void
    {
        $extraction = $this->message->ai_extraction;

        if (!$extraction || !($extraction['has_order_intent'] ?? false)) {
            return;
        }

        if (!$this->hasMinimumOrderData($extraction)) {
            Log::info('AI order: insufficient data to auto-create', [
                'message_id' => $this->message->id,
                'fields'     => array_keys(array_filter($extraction)),
            ]);
            return;
        }

        $merchant = $this->conversation->merchant;
        if (!$merchant) return;

        // Deduplicate: skip if an AI draft order was already made for this conversation in the last 30 min
        $alreadyCreated = Order::where('conversation_id', $this->conversation->id)
            ->where('is_ai_generated', true)
            ->where('created_at', '>', now()->subMinutes(30))
            ->exists();

        if ($alreadyCreated) {
            Log::info('AI order: duplicate skipped', ['conversation_id' => $this->conversation->id]);
            return;
        }

        // Match the product intent to a real product
        $matchedProduct = $this->matchProduct($merchant, $extraction['product_intent'] ?? null);

        if (!$matchedProduct) {
            Log::info('AI order: no product match found', [
                'intent'      => $extraction['product_intent'] ?? null,
                'merchant_id' => $merchant->id,
            ]);
            return;
        }

        if ($matchedProduct->stock <= 0) {
            Log::info('AI order: matched product is out of stock', [
                'product' => $matchedProduct->name,
            ]);
            return;
        }

        $qty          = max(1, (int) ($extraction['quantity'] ?? 1));
        $price        = (float) $matchedProduct->price;
        $shippingCost = 3.00;
        $subtotal     = round($qty * $price, 2);
        $total        = round($subtotal + $shippingCost, 2);

        $order = Order::create([
            'merchant_id'           => $merchant->id,
            'contact_id'            => $this->conversation->contact_id,
            'conversation_id'       => $this->conversation->id,
            'customer_name'         => $extraction['customer_name']
                                        ?? $this->conversation->contact?->name
                                        ?? 'Unknown',
            'customer_phone'        => $extraction['phone_number']
                                        ?? $this->conversation->contact?->phone,
            'shipping_city'         => $extraction['city'] ?? '',
            'shipping_neighborhood' => $extraction['neighborhood'] ?? null,
            'shipping_street'       => $extraction['street_address'] ?? '',
            'payment_method'        => 'cod',
            'payment_status'        => 'pending',
            'fulfillment_status'    => 'draft',
            'items'                 => [[
                'name'  => $matchedProduct->name,
                'sku'   => $matchedProduct->sku,
                'qty'   => $qty,
                'price' => $price,
            ]],
            'subtotal'              => $subtotal,
            'shipping_cost'         => $shippingCost,
            'total'                 => $total,
            'is_ai_generated'       => true,
            'ai_confidence'         => [
                'matched_product' => $matchedProduct->name,
                'product_intent'  => $extraction['product_intent'] ?? null,
            ],
            'internal_notes'        => 'Auto-created by AI assistant. Review and confirm before dispatching.',
        ]);

        Log::info('AI order auto-created as draft', [
            'order_id'        => $order->id,
            'order_number'    => $order->order_number,
            'merchant_id'     => $merchant->id,
            'conversation_id' => $this->conversation->id,
            'product'         => $matchedProduct->name,
            'total'           => $total,
        ]);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    /**
     * All five fields must be present to attempt auto-creation.
     */
    private function hasMinimumOrderData(array $extraction): bool
    {
        return !empty($extraction['customer_name'])
            && !empty($extraction['phone_number'])
            && !empty($extraction['city'])
            && !empty($extraction['street_address'])
            && !empty($extraction['product_intent'])
            && ($extraction['product_intent'] !== 'Unknown Product');
    }

    /**
     * Fuzzy-match the customer's stated product intent to an active product
     * in the merchant's catalogue.
     */
    private function matchProduct(\App\Models\Merchant $merchant, ?string $intent): ?\App\Models\Product
    {
        if (!$intent) return null;

        $products = $merchant->products()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->get();

        if ($products->isEmpty()) return null;

        $intentLower = strtolower(trim($intent));
        $bestMatch   = null;
        $bestScore   = 0.0;

        foreach ($products as $product) {
            $nameLower = strtolower($product->name);

            // Exact match — return immediately
            if ($nameLower === $intentLower) return $product;

            // Substring containment
            if (str_contains($nameLower, $intentLower) || str_contains($intentLower, $nameLower)) {
                return $product;
            }

            // Fuzzy similarity (threshold: 60%)
            similar_text($intentLower, $nameLower, $percent);
            if ($percent > $bestScore && $percent >= 60.0) {
                $bestScore = $percent;
                $bestMatch = $product;
            }
        }

        return $bestMatch;
    }
}
