<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AiSettingsController extends Controller
{
    /**
     * Show the AI settings page.
     */
    public function show(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        $settings = $merchant->settings ?? [];
        $aiInfo   = $settings['ai_business_info'] ?? [];

        $limit     = \App\Models\Merchant::AI_MONTHLY_LIMITS[$merchant->subscription_tier ?? 'starter'] ?? 0;
        $remaining = $merchant->aiMessagesRemainingAttribute;

        return Inertia::render('Settings/Ai', [
            'ai_enabled'  => $merchant->ai_enabled,
            'ai_settings' => [
                'description'   => $aiInfo['description']   ?? '',
                'language'      => $aiInfo['language']       ?? 'auto',
                'greeting'      => $aiInfo['greeting']       ?? '',
                'return_policy' => $aiInfo['return_policy']  ?? '',
                'shipping_info' => $aiInfo['shipping_info']  ?? '',
                'business_hours'=> $aiInfo['business_hours'] ?? '',
                'faqs'          => $aiInfo['faqs']           ?? [],
            ],
            'usage' => [
                'used'      => $merchant->ai_monthly_messages_count,
                'limit'     => $limit,          // null = unlimited
                'remaining' => $remaining,       // null = unlimited
            ],
        ]);
    }

    /**
     * Save AI business info settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'description'    => 'nullable|string|max:2000',
            'language'       => 'required|in:auto,en,sq',
            'greeting'       => 'nullable|string|max:500',
            'return_policy'  => 'nullable|string|max:1000',
            'shipping_info'  => 'nullable|string|max:1000',
            'business_hours' => 'nullable|string|max:500',
            'faqs'           => 'nullable|array|max:20',
            'faqs.*.q'       => 'required|string|max:300',
            'faqs.*.a'       => 'required|string|max:500',
        ]);

        $current = $merchant->settings ?? [];
        $current['ai_business_info'] = array_filter($validated, fn ($v) => $v !== null);

        $merchant->update(['settings' => $current]);

        return back()->with('success', 'AI settings saved.');
    }

    /**
     * Toggle AI auto-reply on or off.
     */
    public function toggle(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $merchant->update(['ai_enabled' => !$merchant->ai_enabled]);

        $status = $merchant->ai_enabled ? 'enabled' : 'disabled';

        return back()->with('success', "AI auto-reply {$status}.");
    }
}
