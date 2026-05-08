<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\AiSettingsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CsatController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\TeamActivityController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ReplyTemplateController;
use App\Http\Controllers\ScheduledMessageController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\WhatsAppCatalogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
});

// Email verification routes (auth required, but not yet verified)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing.index');
})->name('home');

Route::get('/features', function () {
    return view('landing.features');
})->name('features');

Route::get('/pricing', function () {
    return view('landing.pricing');
})->name('pricing');

Route::get('/solutions', function () {
    return view('landing.solutions');
})->name('solutions');

Route::get('/resources', function () {
    return view('landing.resources');
})->name('resources');

Route::get('/privacy-policy', function () {
    return view('landing.privacy');
})->name('privacy');

Route::get('/terms-of-service', function () {
    return view('landing.terms');
})->name('terms');

// Authenticated routes (all require verified email)
Route::middleware(['auth'])->group(function () { // 'verified' removed temporarily

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Onboarding completion
    Route::post('/onboarding/complete', function (\Illuminate\Http\Request $request) {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
        ]);

        $merchant->update([
            ...$validated,
            'onboarding_completed' => true,
        ]);

        return redirect()->route('subscription.index')->with('success', 'Welcome to Nexus! You have a 30-day free Pro trial. Explore your plan options below.');
    })->name('onboarding.complete');

    // Inbox
    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/{conversation}', [InboxController::class, 'show'])->name('inbox.show');
    Route::post('/inbox/{conversation}/reply', [InboxController::class, 'reply'])
        ->name('inbox.reply')
        ->middleware('throttle:60,1');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/dispatch', [OrderController::class, 'dispatch'])->name('orders.dispatch');

    // Contacts
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

    // Broadcasts
    Route::get('/broadcasts', [BroadcastController::class, 'index'])->name('broadcasts.index');
    Route::post('/broadcasts', [BroadcastController::class, 'store'])->name('broadcasts.store');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // ── Tier 1 Features ──────────────────────────────────────────────────

    // CSV Exports
    Route::get('/export/orders', [ExportController::class, 'orders'])->name('export.orders');
    Route::get('/export/contacts', [ExportController::class, 'contacts'])->name('export.contacts');
    Route::get('/export/products', [ExportController::class, 'products'])->name('export.products');

    // Tags & Segmentation
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
    Route::post('/contacts/{contact}/tag', [TagController::class, 'toggleContact'])->name('contacts.tag.toggle');
    Route::post('/tags/rules', [TagController::class, 'storeRule'])->name('tags.rules.store');
    Route::delete('/tags/rules/{rule}', [TagController::class, 'destroyRule'])->name('tags.rules.destroy');
    Route::post('/tags/rules/run', [TagController::class, 'runRules'])->name('tags.rules.run');

    // Quick Reply Templates
    Route::get('/templates', [ReplyTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/search', [ReplyTemplateController::class, 'search'])->name('templates.search');
    Route::post('/templates', [ReplyTemplateController::class, 'store'])->name('templates.store');
    Route::put('/templates/{template}', [ReplyTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [ReplyTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::post('/templates/{template}/use', [ReplyTemplateController::class, 'use'])->name('templates.use');

    // Scheduled Messages & Follow-Up Sequences
    Route::post('/scheduled-messages', [ScheduledMessageController::class, 'store'])->name('scheduled.store');
    Route::delete('/scheduled-messages/{scheduledMessage}', [ScheduledMessageController::class, 'destroy'])->name('scheduled.destroy');
    Route::get('/follow-ups', [ScheduledMessageController::class, 'followUps'])->name('followups.index');
    Route::post('/follow-ups', [ScheduledMessageController::class, 'storeSequence'])->name('followups.store');
    Route::post('/follow-ups/{sequence}/toggle', [ScheduledMessageController::class, 'toggleSequence'])->name('followups.toggle');
    Route::delete('/follow-ups/{sequence}', [ScheduledMessageController::class, 'destroySequence'])->name('followups.destroy');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/orders/{order}/invoice', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

    // Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::put('/coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');
    Route::post('/coupons/validate', [CouponController::class, 'validate_code'])->name('coupons.validate');

    // CSAT
    Route::get('/csat', [CsatController::class, 'index'])->name('csat.index');
    Route::post('/orders/{order}/survey', [CsatController::class, 'send'])->name('csat.send');

    // Returns & Refunds
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::post('/orders/{order}/return', [ReturnController::class, 'store'])->name('returns.store');
    Route::patch('/returns/{return}/status', [ReturnController::class, 'updateStatus'])->name('returns.update-status');

    // Team Activity
    Route::get('/team-activity', [TeamActivityController::class, 'index'])->name('team-activity.index');

    // Demand Forecast
    Route::get('/forecast', [ForecastController::class, 'index'])->name('forecast.index');

    // Storefront
    Route::get('/storefront', [StorefrontController::class, 'settings'])->name('storefront.settings');

    // WhatsApp Catalog
    Route::get('/whatsapp-catalog', [WhatsAppCatalogController::class, 'index'])->name('wa-catalog.index');
    Route::post('/whatsapp-catalog/sync', [WhatsAppCatalogController::class, 'sync'])->name('wa-catalog.sync');
    Route::post('/whatsapp-catalog/settings', [WhatsAppCatalogController::class, 'saveCatalogId'])->name('wa-catalog.settings');

    // Admin-only routes (requires the 'admin' role)
    Route::middleware('role:admin')->group(function () {
        Route::delete('/inbox/{conversation}', [InboxController::class, 'destroy'])->name('inbox.destroy');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.account');
        Route::put('/settings/channels', [SettingsController::class, 'updateChannels'])->name('settings.channels');
        Route::post('/settings/agents', [SettingsController::class, 'storeAgent'])->name('settings.agents.store');
        Route::delete('/settings/agents/{agent}', [SettingsController::class, 'destroyAgent'])->name('settings.agents.destroy');
        Route::get('/subscription', [SettingsController::class, 'subscription'])->name('subscription.index');

        // AI settings — available to Pro and Ultimate plans
        Route::get('/settings/ai', [AiSettingsController::class, 'show'])->name('settings.ai');
        Route::put('/settings/ai', [AiSettingsController::class, 'update'])->name('settings.ai.update');
        Route::post('/settings/ai/toggle', [AiSettingsController::class, 'toggle'])->name('settings.ai.toggle');

        // Meta OAuth — connect Facebook Pages & Instagram automatically
        Route::get('/auth/meta', [OAuthController::class, 'redirectToMeta'])->name('oauth.meta.redirect');
        Route::get('/auth/meta/callback', [OAuthController::class, 'handleMetaCallback'])->name('oauth.meta.callback');
        Route::post('/auth/meta/select-page', [OAuthController::class, 'selectPage'])->name('oauth.meta.select-page');
        Route::delete('/auth/meta', [OAuthController::class, 'disconnectMeta'])->name('oauth.meta.disconnect');

        // WhatsApp Embedded Signup
        Route::post('/auth/whatsapp/callback', [OAuthController::class, 'whatsappEmbeddedSignup'])->name('oauth.whatsapp.callback');
        Route::delete('/auth/whatsapp', [OAuthController::class, 'disconnectWhatsapp'])->name('oauth.whatsapp.disconnect');
    });
});

// Public CSAT feedback (no auth required)
Route::get('/feedback/{token}', [CsatController::class, 'feedback'])->name('csat.feedback');
Route::post('/feedback/{token}', [CsatController::class, 'submitFeedback'])->name('csat.submit');

// Public Storefront (no auth required)
Route::get('/shop/{slug}', [StorefrontController::class, 'show'])->name('storefront.show');
