<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        $contacts = $merchant->contacts()
            ->when($request->input('search'), function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            })
            ->when($request->input('platform'), fn ($q, $p) => $q->where('platform', $p))
            ->withCount(['orders', 'conversations'])
            ->latest()
            ->paginate(25);

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'filters' => $request->only(['search', 'platform']),
        ]);
    }

    public function show(Request $request, Contact $contact): Response
    {
        abort_unless($contact->merchant_id === $request->user()->merchant?->id, 403);

        $contact->load(['conversations' => fn ($q) => $q->latest()->take(5), 'orders' => fn ($q) => $q->latest()->take(10)]);
        $contact->loadCount(['orders', 'conversations']);

        return Inertia::render('Contacts/Show', [
            'contact' => $contact,
        ]);
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        abort_unless($contact->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'street_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:2000',
        ]);

        $contact->update($validated);

        return back()->with('success', 'Contact updated.');
    }
}
