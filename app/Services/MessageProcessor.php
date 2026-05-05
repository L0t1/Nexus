<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Merchant;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class MessageProcessor
{
    public function __construct(
        private AiExtractionService $aiService
    ) {}

    /**
     * Process a Meta (Instagram/Facebook) webhook payload.
     */
    public function processWebhook(array $payload): void
    {
        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['messaging'] ?? [] as $event) {
                if (isset($event['message'])) {
                    $this->handleIncomingMessage(
                        platform: $this->detectPlatform($payload),
                        senderId: $event['sender']['id'] ?? null,
                        recipientId: $event['recipient']['id'] ?? null,
                        messageId: $event['message']['mid'] ?? null,
                        text: $event['message']['text'] ?? null,
                        attachments: $event['message']['attachments'] ?? [],
                        timestamp: $event['timestamp'] ?? time(),
                    );
                } elseif (isset($event['delivery'])) {
                    $mids = $event['delivery']['mids'] ?? [];
                    if (!empty($mids)) {
                        \App\Models\Message::whereIn('external_message_id', $mids)->update(['status' => 'delivered']);
                    }
                } elseif (isset($event['read'])) {
                    $watermark = $event['read']['watermark'] ?? 0;
                    $mid = $event['read']['mid'] ?? null;
                    $senderId = $event['sender']['id'] ?? null;
                    
                    if ($mid) {
                        \App\Models\Message::where('external_message_id', $mid)
                            ->update(['status' => 'read']);
                    }
                    if ($watermark && $senderId) {
                        $contact = \App\Models\Contact::where('external_id', $senderId)->first();
                        if ($contact) {
                            $contact->messages()->where('direction', 'outbound')
                                ->where('status', '!=', 'read')
                                ->update(['status' => 'read']);
                        }
                    }
                }
            }
        }
    }

    /**
     * Process a WhatsApp Cloud API message.
     */
    public function processWhatsAppMessage(array $value): void
    {
        $messages = $value['messages'] ?? [];
        $contacts = $value['contacts'] ?? [];
        $metadata = $value['metadata'] ?? [];

        foreach ($messages as $msg) {
            $contactInfo = collect($contacts)->firstWhere('wa_id', $msg['from']);

            $this->handleIncomingMessage(
                platform: 'whatsapp',
                senderId: $msg['from'] ?? null,
                recipientId: $metadata['phone_number_id'] ?? null,
                messageId: $msg['id'] ?? null,
                text: $msg['text']['body'] ?? null,
                attachments: [],
                timestamp: $msg['timestamp'] ?? time(),
                contactName: $contactInfo['profile']['name'] ?? null,
            );
        }

        $statuses = $value['statuses'] ?? [];
        $allowedStatuses = ['sent', 'delivered', 'read', 'failed'];
        foreach ($statuses as $status) {
            $wamId = $status['id'] ?? null;
            $statusValue = $status['status'] ?? null;
            if ($wamId && $statusValue && in_array($statusValue, $allowedStatuses, true)) {
                \App\Models\Message::where('external_message_id', $wamId)->update(['status' => $statusValue]);
            }
        }
    }

    /**
     * Core message handler — normalizes all platforms into unified Message model.
     */
    private function handleIncomingMessage(
        string $platform,
        ?string $senderId,
        ?string $recipientId,
        ?string $messageId,
        ?string $text,
        array $attachments,
        int $timestamp,
        ?string $contactName = null,
    ): void {
        if (!$senderId || !$recipientId) return;

        // Find the merchant by their connected platform credentials
        $merchant = $this->findMerchant($platform, $recipientId);
        if (!$merchant) {
            Log::warning("No merchant found for {$platform} recipient: {$recipientId}");
            return;
        }

        if (!$contactName) {
            $contactName = $this->fetchProfileName($senderId, $platform, $merchant);
        }

        // Find or create the contact
        $contact = Contact::firstOrCreate(
            ['merchant_id' => $merchant->id, 'external_id' => $senderId, 'platform' => $platform],
            ['name' => $contactName ?: 'Unknown User', 'last_contacted_at' => now()]
        );
        
        // Update name if we successfully fetched it this time
        if ($contactName && ($contact->name === 'Unknown User' || !$contact->name)) {
            $contact->update(['name' => $contactName]);
        }
        
        $contact->update(['last_contacted_at' => now()]);

        // Find or create conversation thread
        $conversation = Conversation::withTrashed()->firstOrCreate(
            ['merchant_id' => $merchant->id, 'external_thread_id' => $senderId],
            [
                'contact_id' => $contact->id,
                'platform' => $platform,
                'status' => 'active',
            ]
        );

        if ($conversation->trashed()) {
            $conversation->restore();
            $conversation->update(['status' => 'active']);
        }

        // Detect message type
        $type = 'text';
        $media = null;
        if (!empty($attachments)) {
            $att = $attachments[0];
            $type = $att['type'] ?? 'document';
            $media = ['url' => $att['payload']['url'] ?? null, 'mime_type' => $att['type'] ?? null];
        }

        // Store the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'contact_id' => $contact->id,
            'external_message_id' => $messageId,
            'direction' => 'inbound',
            'type' => $type,
            'body' => $text,
            'media' => $media,
            'status' => 'delivered',
        ]);

        // Update conversation metadata
        $conversation->updateLastMessage($message);

        // Run AI extraction on text messages
        if ($text && strlen($text) > 10) {
            \App\Jobs\ProcessAiExtraction::dispatch($message);
        }
    }

    private function detectPlatform(array $payload): string
    {
        $object = $payload['object'] ?? '';
        return match ($object) {
            'instagram' => 'instagram',
            'page' => 'facebook',
            'whatsapp_business_account' => 'whatsapp',
            default => 'manual',
        };
    }

    private function findMerchant(string $platform, string $recipientId): ?Merchant
    {
        // Try to match by stored page ID or phone number ID in meta_credentials
        $credentialKey = match ($platform) {
            'instagram' => 'instagram_page_id',
            'facebook' => 'facebook_page_id',
            'whatsapp' => 'whatsapp_phone_id',
            default => null,
        };

        if (!$credentialKey) {
            Log::warning('Unknown platform in webhook', ['platform' => $platform]);
            return null;
        }

        // Use cursor() to avoid loading all merchants into memory at once.
        // meta_credentials is encrypted JSON so we can't query it in SQL.
        $merchant = Merchant::where('is_active', true)
            ->cursor()
            ->first(function ($m) use ($credentialKey, $recipientId) {
                $creds = $m->meta_credentials ?? [];
                return ($creds[$credentialKey] ?? null) === $recipientId;
            });

        if (!$merchant) {
            Log::warning("No merchant found for {$platform} recipient: {$recipientId}. Message dropped.");
        }

        return $merchant;
    }

    private function fetchProfileName(string $senderId, string $platform, Merchant $merchant): ?string
    {
        if (!in_array($platform, ['facebook', 'instagram'])) return null;

        $token = $merchant->meta_credentials['facebook_token'] ?? config('services.meta.page_token');
        if (!$token) return null;

        try {
            $fields = $platform === 'instagram' ? 'name,profile_pic' : 'first_name,last_name,profile_pic';
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get("https://graph.facebook.com/v19.0/{$senderId}", [
                    'fields' => $fields,
                    'access_token' => $token
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $name = $data['name'] ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
                return $name ?: null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
