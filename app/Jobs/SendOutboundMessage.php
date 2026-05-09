<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOutboundMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public Message $message,
        public Conversation $conversation
    ) {}

    public function handle(): void
    {
        $merchant = $this->conversation->merchant;
        $contact = $this->conversation->contact;
        $platform = $this->conversation->platform;

        if (!$contact?->external_id) {
            Log::warning('Cannot send outbound: no external_id', ['message_id' => $this->message->id]);
            $this->message->update(['status' => 'failed']);
            return;
        }

        try {
            match ($platform) {
                'instagram', 'facebook' => $this->sendViaMetaMessenger($merchant, $contact->external_id),
                'whatsapp' => $this->sendViaWhatsApp($merchant, $contact->external_id),
                default => Log::info('Manual platform — no outbound API', ['platform' => $platform]),
            };

            $this->message->update(['status' => 'delivered']);
        } catch (\Exception $e) {
            Log::error('Outbound message failed', [
                'message_id' => $this->message->id,
                'platform' => $platform,
                'error' => $e->getMessage(),
            ]);
            $this->message->update(['status' => 'failed']);
            throw $e; // Re-throw for retry
        }
    }

    private function sendViaMetaMessenger(Merchant $merchant, string $recipientId): void
    {
        $channel = $this->conversation->channel;
        
        $token = $channel?->credentials['page_token']
            ?? $channel?->credentials['instagram_token']
            ?? $merchant->meta_credentials['page_token']
            ?? config('services.meta.page_token');

        if (!$token) {
            throw new \RuntimeException('No Meta page token configured for channel');
        }

        $response = Http::withToken($token)
            ->post('https://graph.facebook.com/v19.0/me/messages', [
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $this->message->body],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException("Meta API error: {$response->body()}");
        }

        // Store external message ID for tracking
        $externalId = $response->json('message_id');
        if ($externalId) {
            $this->message->update(['external_message_id' => $externalId]);
        }
    }

    private function sendViaWhatsApp(Merchant $merchant, string $recipientPhone): void
    {
        $channel = $this->conversation->channel;

        $token = $channel?->credentials['whatsapp_token']
            ?? $merchant->meta_credentials['whatsapp_token']
            ?? config('services.whatsapp.token');
            
        $phoneId = $channel?->external_id
            ?? $merchant->meta_credentials['whatsapp_phone_id']
            ?? config('services.whatsapp.phone_id');

        if (!$token || !$phoneId) {
            throw new \RuntimeException('No WhatsApp credentials configured for channel');
        }

        $response = Http::withToken($token)
            ->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => preg_replace('/[^0-9]/', '', $recipientPhone),
                'type' => 'text',
                'text' => ['body' => $this->message->body],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException("WhatsApp API error: {$response->body()}");
        }

        $wamId = $response->json('messages.0.id');
        if ($wamId) {
            $this->message->update(['external_message_id' => $wamId]);
        }
    }
}
