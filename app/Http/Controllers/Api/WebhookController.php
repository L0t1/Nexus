<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Merchant;
use App\Services\MessageProcessor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private MessageProcessor $processor
    ) {}

    /**
     * Meta Webhook Verification (GET)
     */
    public function verify(Request $request): mixed
    {
        $verifyToken = config('services.meta.verify_token');

        // Guard: if token is not configured, refuse all verification requests
        // (prevents null === null bypass when META_VERIFY_TOKEN is missing)
        if (!$verifyToken) {
            Log::critical('META_VERIFY_TOKEN is not configured. Webhook verification refused.');
            return response('Server misconfiguration', 500);
        }

        if ($request->input('hub_mode') === 'subscribe'
            && hash_equals($verifyToken, (string) $request->input('hub_verify_token', ''))) {
            return response($request->input('hub_challenge'), 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Meta Webhook Receiver (POST) — handles IG, FB, WA messages
     */
    public function receive(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Meta webhook received', [
            'object'      => $payload['object'] ?? 'unknown',
            'entry_count' => count($payload['entry'] ?? []),
        ]);

        // Validate signature
        $signature = $request->header('X-Hub-Signature-256');
        if (!$this->validateSignature($request->getContent(), $signature)) {
            Log::warning('Invalid webhook signature');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Process the webhook payload
        try {
            $this->processor->processWebhook($payload);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', ['error' => $e->getMessage()]);
        }

        // Always return 200 to Meta
        return response()->json(['status' => 'received']);
    }

    /**
     * WhatsApp Cloud API Webhook
     */
    public function whatsapp(Request $request): JsonResponse
    {
        // Validate HMAC signature (same secret as Meta)
        $signature = $request->header('X-Hub-Signature-256');
        if (!$this->validateSignature($request->getContent(), $signature)) {
            Log::warning('Invalid WhatsApp webhook signature');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $payload = $request->all();

        Log::info('WhatsApp webhook received', [
            'entry_count' => count($payload['entry'] ?? []),
        ]);

        if (isset($payload['entry'])) {
            foreach ($payload['entry'] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    if ($change['field'] === 'messages') {
                        $this->processor->processWhatsAppMessage($change['value']);
                    }
                }
            }
        }

        return response()->json(['status' => 'received']);
    }

    private function validateSignature(string $payload, ?string $signature): bool
    {
        if (!$signature) return false;
        $secret = config('services.meta.app_secret');
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }
}
