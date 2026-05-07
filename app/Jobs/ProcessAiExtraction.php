<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\AiExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAiExtraction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(public Message $message)
    {
    }

    public function handle(AiExtractionService $aiService): void
    {
        $aiService->extractOrderData($this->message);

        // After extraction, check if the AI detected a complete order intent.
        // If so, dispatch the auto-order creation job (Ultimate plan feature).
        $message = $this->message->fresh();

        if ($message && ($message->ai_extraction['has_order_intent'] ?? false)) {
            $conversation = $message->conversation;

            if ($conversation && $conversation->merchant?->hasFeature('ai_chat')) {
                \App\Jobs\ProcessAiOrder::dispatch($message, $conversation);
            }
        }
    }
}
