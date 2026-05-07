<?php

namespace App\Jobs;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiChatService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ProcessAiReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 15;

    public function __construct(
        public Message $inboundMessage,
        public Conversation $conversation,
    ) {}

    public function handle(AiChatService $aiChat): void
    {
        $merchant = $this->conversation->merchant;

        if (!$merchant || !$merchant->canSendAiReply()) {
            return;
        }

        // Per-conversation rate limit: max 3 AI replies per 60 seconds.
        // Protects against spam floods burning the merchant's monthly quota.
        $throttleKey = "ai_reply:conv:{$this->conversation->id}";
        if (!RateLimiter::attempt($throttleKey, 3, fn () => true, 60)) {
            Log::info('AI reply throttled (per-conversation limit)', [
                'conversation_id' => $this->conversation->id,
            ]);
            return;
        }

        $replyText = $aiChat->generateReply($this->inboundMessage, $this->conversation);

        if (!$replyText) {
            return;
        }

        // Store the outbound message
        $outbound = Message::create([
            'conversation_id'    => $this->conversation->id,
            'contact_id'         => $this->conversation->contact_id,
            'direction'          => 'outbound',
            'type'               => 'text',
            'body'               => $replyText,
            'status'             => 'pending',
            'is_ai_generated'    => true,
        ]);

        // Update conversation last-message metadata
        $this->conversation->updateLastMessage($outbound);

        // Physically send the reply via the platform
        SendOutboundMessage::dispatch($outbound, $this->conversation);

        // Count against the merchant's monthly quota
        $merchant->incrementAiMessageCount();

        Log::info('AI auto-reply dispatched', [
            'merchant_id'     => $merchant->id,
            'conversation_id' => $this->conversation->id,
            'message_id'      => $outbound->id,
        ]);
    }
}
