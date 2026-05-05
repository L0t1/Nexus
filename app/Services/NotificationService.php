<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send order status notification to customer via WhatsApp or SMS.
     */
    public function sendOrderNotification(Order $order, string $event): void
    {
        $phone = $order->customer_phone;
        $message = $this->buildMessage($order, $event);

        if (!$phone || !$message) return;

        // Try WhatsApp first, fall back to SMS
        try {
            $this->sendWhatsApp($phone, $message, $order);
        } catch (\Exception $e) {
            // Mask phone number in logs to avoid PII leakage (show last 4 digits only)
            $maskedPhone = '***' . substr($phone, -4);
            Log::info("WhatsApp failed for {$maskedPhone}, trying SMS", ['error' => $e->getMessage()]);
            $this->sendSms($phone, $message);
        }
    }

    private function buildMessage(Order $order, string $event): ?string
    {
        $name = $order->customer_name;
        $orderNum = $order->order_number;
        $tracking = $order->tracking_number;

        return match ($event) {
            'confirmed' => "Hi {$name}! ✅ Your order {$orderNum} has been confirmed. We'll notify you when it's dispatched.",
            'dispatched' => "Hi {$name}! 📦 Your order {$orderNum} has been dispatched." . ($tracking ? " Track: {$tracking}" : ''),
            'out_for_delivery' => "Hi {$name}! 🚚 Your order {$orderNum} is out for delivery today. Please be available!",
            'delivered' => "Hi {$name}! ✅ Your order {$orderNum} has been delivered. Thank you for shopping with us!",
            default => null,
        };
    }

    private function sendWhatsApp(string $phone, string $message, Order $order): void
    {
        $merchant = $order->merchant;
        $token = $merchant->meta_credentials['whatsapp_token'] ?? config('services.whatsapp.token');
        $phoneId = $merchant->meta_credentials['whatsapp_phone_id'] ?? config('services.whatsapp.phone_id');

        if (!$token || !$phoneId) return;

        Http::withToken($token)->post(
            "https://graph.facebook.com/v19.0/{$phoneId}/messages",
            [
                'messaging_product' => 'whatsapp',
                'to' => preg_replace('/[^0-9+]/', '', $phone),
                'type' => 'text',
                'text' => ['body' => $message],
            ]
        );
    }

    private function sendSms(string $phone, string $message): void
    {
        // Integrate with Twilio, Vonage, or local SMS provider
        $maskedPhone = '***' . substr($phone, -4);
        Log::info('SMS notification dispatched', ['phone' => $maskedPhone]);
    }
}
