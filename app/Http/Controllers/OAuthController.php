<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OAuthController extends Controller
{
    private string $graphUrl = 'https://graph.facebook.com/v19.0';

    // ─── Step 1: Redirect to Facebook OAuth ──────────────────────────────────

    public function redirectToMeta(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('meta_oauth_state', $state);

        $params = http_build_query([
            'client_id'     => config('services.meta.app_id'),
            'redirect_uri'  => route('oauth.meta.callback'),
            'scope'         => 'pages_messaging,pages_manage_metadata,pages_read_engagement,instagram_basic,instagram_manage_messages',
            'state'         => $state,
            'response_type' => 'code',
        ]);

        return redirect("https://www.facebook.com/v19.0/dialog/oauth?{$params}");
    }

    // ─── Step 2: Handle callback from Facebook ────────────────────────────────

    public function handleMetaCallback(Request $request): RedirectResponse|Response
    {
        // Validate CSRF state
        $storedState = $request->session()->pull('meta_oauth_state', '');
        if (!$storedState || !hash_equals($storedState, (string) $request->input('state', ''))) {
            return redirect()->route('settings.index')->with('error', 'Security check failed. Please try again.');
        }

        if ($request->has('error')) {
            return redirect()->route('settings.index')->with('error', 'Facebook authorization was cancelled.');
        }

        // Exchange authorization code for user access token
        $tokenResponse = Http::get("{$this->graphUrl}/oauth/access_token", [
            'client_id'     => config('services.meta.app_id'),
            'client_secret' => config('services.meta.app_secret'),
            'redirect_uri'  => route('oauth.meta.callback'),
            'code'          => $request->input('code'),
        ]);

        if (!$tokenResponse->successful()) {
            Log::error('Meta OAuth token exchange failed', ['response' => $tokenResponse->body()]);
            return redirect()->route('settings.index')->with('error', 'Failed to connect with Facebook. Please try again.');
        }

        $userToken = $tokenResponse->json('access_token');

        // Fetch all Pages this user manages
        $pagesResponse = Http::withToken($userToken)
            ->get("{$this->graphUrl}/me/accounts", [
                'fields' => 'id,name,access_token,instagram_business_account',
            ]);

        if (!$pagesResponse->successful()) {
            Log::error('Meta pages fetch failed', ['response' => $pagesResponse->body()]);
            return redirect()->route('settings.index')->with('error', 'Failed to fetch your Facebook Pages.');
        }

        $pages = $pagesResponse->json('data', []);

        if (empty($pages)) {
            return redirect()->route('settings.index')->with('error', 'No Facebook Pages found on your account.');
        }

        if (count($pages) === 1) {
            $this->savePageCredentials($request->user(), $pages[0]);
            return redirect()->route('settings.index')->with('success', 'Facebook connected successfully!');
        }

        // Multiple pages — let merchant choose which one to connect
        $request->session()->put('meta_oauth_pages', $pages);
        return Inertia::render('Settings/SelectPage', ['pages' => $pages]);
    }

    // ─── Step 3 (if multiple pages): Save selected page ──────────────────────

    public function selectPage(Request $request): RedirectResponse
    {
        $validated = $request->validate(['page_id' => 'required|string']);
        $pages = $request->session()->pull('meta_oauth_pages', []);
        $page  = collect($pages)->firstWhere('id', $validated['page_id']);

        if (!$page) {
            return redirect()->route('settings.index')->with('error', 'Invalid page selection.');
        }

        $this->savePageCredentials($request->user(), $page);
        return redirect()->route('settings.index')->with('success', 'Facebook connected successfully!');
    }

    // ─── Disconnect ───────────────────────────────────────────────────────────

    public function disconnectMeta(Request $request): RedirectResponse
    {
        $request->validate(['channel_id' => 'required|exists:channels,id']);
        $channel = $request->user()->merchant->channels()->findOrFail($request->channel_id);
        
        $channel->delete();
        return back()->with('success', 'Channel disconnected.');
    }

    // ─── WhatsApp Embedded Signup ─────────────────────────────────────────────

    public function whatsappEmbeddedSignup(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'code'            => 'nullable|string',
            'phone_number_id' => 'nullable|string',
            'waba_id'         => 'nullable|string',
        ]);

        $phoneNumberId = $validated['phone_number_id'] ?? null;
        $userToken     = null;

        if (!$phoneNumberId && !empty($validated['code'])) {
            $tokenResponse = Http::get("{$this->graphUrl}/oauth/access_token", [
                'client_id'     => config('services.meta.app_id'),
                'client_secret' => config('services.meta.app_secret'),
                'code'          => $validated['code'],
            ]);

            if ($tokenResponse->successful()) {
                $userToken = $tokenResponse->json('access_token');
                $phonesResponse = Http::withToken($userToken)->get("{$this->graphUrl}/me/whatsapp_business_accounts", [
                    'fields' => 'id,name,phone_numbers{id,display_phone_number,verified_name}',
                ]);

                if ($phonesResponse->successful()) {
                    foreach ($phonesResponse->json('data', []) as $waba) {
                        $phones = $waba['phone_numbers']['data'] ?? [];
                        if (!empty($phones)) {
                            $phoneNumberId = $phones[0]['id'];
                            $phoneName = $phones[0]['verified_name'] ?? $phones[0]['display_phone_number'];
                            break;
                        }
                    }
                }
            }
        }

        if (!$phoneNumberId) {
            return response()->json(['error' => 'No WhatsApp phone number found.'], 422);
        }

        $merchant = $request->user()->merchant;
        
        $merchant->channels()->updateOrCreate(
            ['platform' => 'whatsapp', 'external_id' => $phoneNumberId],
            [
                'label' => $phoneName ?? 'WhatsApp Business',
                'credentials' => [
                    'whatsapp_token' => $userToken ?? config('services.whatsapp.token'),
                ],
                'is_active' => true
            ]
        );

        return response()->json(['success' => true]);
    }

    public function disconnectWhatsapp(Request $request): RedirectResponse
    {
        $request->validate(['channel_id' => 'required|exists:channels,id']);
        $channel = $request->user()->merchant->channels()->findOrFail($request->channel_id);
        
        $channel->delete();
        return back()->with('success', 'WhatsApp disconnected.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function savePageCredentials(\App\Models\User $user, array $page): void
    {
        $merchant = $user->merchant;

        // Save Facebook Page as a channel
        $merchant->channels()->updateOrCreate(
            ['platform' => 'facebook', 'external_id' => $page['id']],
            [
                'label' => $page['name'],
                'credentials' => ['page_token' => $page['access_token']],
                'is_active' => true
            ]
        );

        // If this page has a linked Instagram Business account, save it as a separate channel
        if (!empty($page['instagram_business_account']['id'])) {
            $merchant->channels()->updateOrCreate(
                ['platform' => 'instagram', 'external_id' => $page['instagram_business_account']['id']],
                [
                    'label' => $page['name'] . ' (Instagram)',
                    'credentials' => ['instagram_token' => $page['access_token']],
                    'is_active' => true
                ]
            );
        }
    }
}
