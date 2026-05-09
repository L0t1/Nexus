<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        return Inertia::render('Settings/Index', [
            'merchant' => array_merge($merchant->toArray(), [
                'meta_credentials' => $merchant->meta_credentials ?? [],
                'channels' => $merchant->channels()->orderBy('platform')->get(),
            ]),
            'user'                  => $request->user()->only('id', 'name', 'email'),
            'agents'                => \App\Models\User::role('agent')->where('merchant_id', $merchant->id)->get(['id', 'name', 'email', 'created_at']),
            'metaAppId'             => config('services.meta.app_id'),
            'metaWhatsappConfigId'  => config('services.meta.whatsapp_config_id'),
        ]);
    }

    public function storeAgent(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'merchant_id' => $merchant->id,
        ]);

        $user->assignRole('agent');

        \App\Models\ActivityLog::record($merchant->id, 'agent.invited', "Invited new agent: {$user->name}.", 'User', $user->id);

        return back()->with('success', 'Agent added successfully.');
    }

    public function destroyAgent(Request $request, \App\Models\User $agent): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        if ($agent->merchant_id !== $merchant->id) {
            abort(403);
        }

        \App\Models\ActivityLog::record($merchant->id, 'agent.removed', "Removed agent: {$agent->name}.", 'User', $agent->id);
        $agent->delete();

        return back()->with('success', 'Agent removed successfully.');
    }

    public function subscription(Request $request): Response
    {
        return Inertia::render('Subscription/Index', [
            'merchant' => $request->user()->merchant,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $merchant->update($validated);
        \App\Models\ActivityLog::record($merchant->id, 'settings.profile_updated', "Updated business profile settings.");

        return back()->with('success', 'Business profile updated.');
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($validated);
        \App\Models\ActivityLog::record($request->user()->merchant_id, 'settings.account_updated', "Updated personal account settings.");

        return back()->with('success', 'Account updated.');
    }

    public function updateChannels(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        // WhatsApp only — Facebook/Instagram are connected via OAuth
        $validated = $request->validate([
            'whatsapp_phone_id' => 'nullable|string|max:64',
            'whatsapp_token'    => 'nullable|string|max:512',
        ]);

        // Merge with existing so OAuth-saved FB/IG tokens are never wiped
        $existing = $merchant->meta_credentials ?? [];
        $merchant->update([
            'meta_credentials' => array_merge($existing, array_filter($validated)),
        ]);

        return back()->with('success', 'Channel settings saved.');
    }
}
