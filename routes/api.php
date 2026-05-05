<?php

use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Meta Webhook endpoints (no session auth — verified by HMAC signature)
// Rate-limited to absorb bursts while blocking floods
Route::prefix('webhooks')->middleware('throttle:300,1')->group(function () {
    Route::get('/meta', [WebhookController::class, 'verify']);
    Route::post('/meta', [WebhookController::class, 'receive']);
    Route::get('/whatsapp', [WebhookController::class, 'verify']);
    Route::post('/whatsapp', [WebhookController::class, 'whatsapp']);
});

// Authenticated API routes
Route::middleware('auth:sanctum')->group(function () {
    // Future: Mobile app API, external integrations
});
