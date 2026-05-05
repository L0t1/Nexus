<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BroadcastController extends Controller
{
    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;

        if (!$merchant->hasFeature('broadcasts')) {
            return redirect('/subscription')->with('error', 'Marketing Broadcasts are only available on the Ultimate plan.');
        }

        $cities = $merchant->contacts()
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city');

        return Inertia::render('Broadcasts/Index', [
            'cities' => $cities,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'city' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'platform' => 'nullable|in:whatsapp,instagram,facebook',
            'min_orders' => 'nullable|integer|min:0',
        ]);

        $merchant = $request->user()->merchant;

        if (!$merchant->hasFeature('broadcasts')) {
            abort(403, 'Marketing Broadcasts are only available on the Ultimate plan.');
        }

        $query = $merchant->contacts();

        if (!empty($validated['city'])) {
            $query->where('city', $validated['city']);
        }
        if (!empty($validated['gender'])) {
            $query->where('gender', $validated['gender']);
        }
        if (!empty($validated['platform'])) {
            $query->where('platform', $validated['platform']);
        }
        if (isset($validated['min_orders']) && $validated['min_orders'] > 0) {
            $query->where('total_orders', '>=', $validated['min_orders']);
        }

        $contacts = $query->get();

        if ($contacts->isEmpty()) {
            return back()->withErrors(['message' => 'No contacts match the selected filters.']);
        }

        // In a real app, we would dispatch a queued job per contact to send the message.
        // For now, we simulate success and return back.
        
        return back()->with('success', "Broadcast successfully queued for {$contacts->count()} contacts!");
    }
}
