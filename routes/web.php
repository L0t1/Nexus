<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingsController;
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
});

// Authenticated routes (all require verified email)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Admin-only routes (requires the 'admin' role)
    Route::middleware('role:admin')->group(function () {
        Route::delete('/inbox/{conversation}', [InboxController::class, 'destroy'])->name('inbox.destroy');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.account');
        Route::put('/settings/channels', [SettingsController::class, 'updateChannels'])->name('settings.channels');
        Route::get('/subscription', [SettingsController::class, 'subscription'])->name('subscription.index');

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
