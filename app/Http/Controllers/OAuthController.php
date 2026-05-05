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
        // Enforce channel limit for Starter plan
        $merchant = $request->user()->merchant;
        if (!$merchant->hasFeature('all_channels')) {
            $creds = $merchant->meta_credentials ?? [];
            if (!empty($creds['whatsapp_phone_id'])) {
                return redirect()->route('settings.index')
                    ->with('error', 'Your Starter plan only allows 1 channel integration. Disconnect WhatsApp first or upgrade to Pro.');
            }
        }

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

        // Fetch all Pages this user manages (each page gets its own long-lived token)
        $pagesResponse = Http::withToken($userToken)
            ->get("{$this->graphUrl}/me/accounts", [
                'fields' => 'id,name,access_token,instagram_business_account',
            ]);

        if (!$pagesResponse->successful()) {
            Log::error('Meta pages fetch failed', ['response' => $pagesResponse->body()]);
            return redirect()->route('settings.index')->with('error', 'Failed to fetch your Facebook Pages. Make sure you have admin access to a Page.');
        }

        $pages = $pagesResponse->json('data', []);

        if (empty($pages)) {
            return redirect()->route('settings.index')
                ->with('error', 'No Facebook Pages found on your account. Please create a Page first or make sure you have admin access.');
        }

        if (count($pages) === 1) {
            $this->savePageCredentials($request->user(), $pages[0]);
            return redirect()->route('settings.index')->with('success', 'Facebook & Instagram connected successfully!');
        }

        // Multiple pages — let merchant choose which one to use
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

        return redirect()->route('settings.index')->with('success', 'Facebook & Instagram connected successfully!');
    }

    // ─── Disconnect ───────────────────────────────────────────────────────────

    public function disconnectMeta(Request $request): RedirectResponse
    {
        $merchant  = $request->user()->merchant;
        $existing  = $merchant->meta_credentials ?? [];

        $merchant->update([
            'meta_credentials' => array_diff_key($existing, array_flip([
                'facebook_page_id',
                'facebook_token',
                'instagram_page_id',
                'instagram_token',
            ])),
        ]);

        return back()->with('success', 'Facebook & Instagram disconnected.');
    }

    // ─── WhatsApp Embedded Signup — receive token from JS SDK callback ────────

    /**
     * After the merchant completes the WhatsApp Embedded Signup flow in the
     * browser, the JS SDK gives us a short-lived code. We exchange it for a
     * user token, then fetch the WABA phone numbers and store them.
     */
    public function whatsappEmbeddedSignup(Request $request): \Illuminate\Http\JsonResponse
    {
        // Enforce channel limit for Starter plan
        $merchant = $request->user()->merchant;
        if (!$merchant->hasFeature('all_channels')) {
            $creds = $merchant->meta_credentials ?? [];
            if (!empty($creds['facebook_page_id'])) {
                return response()->json(['error' => 'Your Starter plan only allows 1 channel integration. Disconnect Facebook first or upgrade to Pro.'], 422);
            }
        }

        $validated = $request->validate([
            'code'            => 'nullable|string',
            'phone_number_id' => 'nullable|string',
            'waba_id'         => 'nullable|string',
        ]);

        $phoneNumberId = $validated['phone_number_id'] ?? null;
        $userToken     = null;

        if (!$phoneNumberId) {
            // Path A: exchange code for token, then fetch phone numbers
            if (empty($validated['code'])) {
                return response()->json(['error' => 'No authorization code received.'], 422);
            }

            $tokenResponse = Http::get("{$this->graphUrl}/oauth/access_token", [
                'client_id'     => config('services.meta.app_id'),
                'client_secret' => config('services.meta.app_secret'),
                'code'          => $validated['code'],
            ]);

            if (!$tokenResponse->successful()) {
                Log::error('WhatsApp Embedded Signup token exchange failed', ['response' => $tokenResponse->body()]);
                return response()->json(['error' => 'Failed to connect WhatsApp. Please try again.'], 422);
            }

            $userToken = $tokenResponse->json('access_token');

            $phonesResponse = Http::withToken($userToken)
                ->get("{$this->graphUrl}/me/whatsapp_business_accounts", [
                    'fields' => 'id,name,phone_numbers{id,display_phone_number,verified_name}',
                ]);

            if (!$phonesResponse->successful()) {
                Log::error('WhatsApp phone numbers fetch failed', ['response' => $phonesResponse->body()]);
                return response()->json(['error' => 'Could not fetch WhatsApp phone numbers.'], 422);
            }

            foreach ($phonesResponse->json('data', []) as $waba) {
                $phones = $waba['phone_numbers']['data'] ?? [];
                if (!empty($phones)) {
                    $phoneNumberId = $phones[0]['id'];
                    break;
                }
            }
        }

        if (!$phoneNumberId) {
            return response()->json(['error' => 'No WhatsApp phone number found on this account.'], 422);
        }

        $merchant = $request->user()->merchant;
        $existing = $merchant->meta_credentials ?? [];

        $creds = ['whatsapp_phone_id' => $phoneNumberId];
        if ($userToken) {
            $creds['whatsapp_token'] = $userToken;
        }

        $merchant->update([
            'meta_credentials' => array_merge($existing, $creds),
        ]);

        Log::info('WhatsApp Embedded Signup complete', [
            'merchant_id'     => $merchant->id,
            'phone_number_id' => $phoneNumberId,
        ]);

        return response()->json(['success' => true]);
    }

    // ─── WhatsApp disconnect ──────────────────────────────────────────────────

    public function disconnectWhatsapp(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        $existing = $merchant->meta_credentials ?? [];

        $merchant->update([
            'meta_credentials' => array_diff_key($existing, array_flip([
                'whatsapp_phone_id',
                'whatsapp_token',
            ])),
        ]);

        return back()->with('success', 'WhatsApp disconnected.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function savePageCredentials(User $user, array $page): void
    {
        $merchant = $user->merchant;
        $existing = $merchant->meta_credentials ?? [];

        $updates = [
            'facebook_page_id' => $page['id'],
            'facebook_token'   => $page['access_token'],
        ];

        // If this page has a linked Instagram Business account, store it too
        if (!empty($page['instagram_business_account']['id'])) {
            $updates['instagram_page_id'] = $page['instagram_business_account']['id'];
            $updates['instagram_token']   = $page['access_token']; // IG uses the same page token
        }

        $merchant->update([
            'meta_credentials' => array_merge($existing, $updates),
        ]);

        Log::info('Meta credentials saved for merchant', [
            'merchant_id' => $merchant->id,
            'has_instagram' => isset($updates['instagram_page_id']),
        ]);
    }
}
