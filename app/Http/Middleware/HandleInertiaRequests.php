<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $merchant = $user?->merchant;

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                ] : null,
                'merchant' => $merchant ? [
                    'id' => $merchant->id,
                    'business_name' => $merchant->business_name,
                    'phone' => $merchant->phone,
                    'country' => $merchant->country,
                    'city' => $merchant->city,
                    'subscription_tier' => $merchant->subscription_tier,
                    'logo_path' => $merchant->logo_path,
                    'features' => $merchant->allowed_features,
                    'meta_credentials' => [
                        'facebook_page_id'  => ($merchant->meta_credentials ?? [])['facebook_page_id'] ?? null,
                        'instagram_page_id' => ($merchant->meta_credentials ?? [])['instagram_page_id'] ?? null,
                        'whatsapp_phone_id' => ($merchant->meta_credentials ?? [])['whatsapp_phone_id'] ?? null,
                    ],
                    'onboarding_completed' => $merchant->onboarding_completed,
                    'subscription_expires_at' => $merchant->subscription_expires_at?->toISOString(),
                ] : null,
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
                'csat_link' => fn () => $request->session()->get('csat_link'),
            ],

            'stats' => $merchant ? [
                'unread_conversations' => fn () => $merchant->conversations()->unread()->count(),
                'pending_orders'       => fn () => $merchant->orders()->pending()->count(),
                'cod_pending'          => fn () => $merchant->orders()->codPending()->count(),
                'cod_amount'           => fn () => (float) $merchant->orders()->codPending()->sum('total'),
            ] : null,
        ];
    }
}

