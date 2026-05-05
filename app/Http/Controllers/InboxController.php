<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InboxController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        $conversations = $merchant->conversations()
            ->with(['contact:id,name,phone,platform,city', 'assignee:id,name'])
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->input('platform'), fn ($q, $platform) => $q->forPlatform($platform))
            ->when($request->input('search'), function ($q, $search) {
                $q->whereHas('contact', fn ($c) => $c->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"));
            })
            ->latest('last_message_at')
            ->paginate(30);

        return Inertia::render('Inbox/Index', [
            'conversations' => $conversations,
            'filters' => $request->only(['status', 'platform', 'search']),
        ]);
    }

    public function show(Request $request, Conversation $conversation): Response
    {
        // Simple ownership check instead of policy
        abort_unless($conversation->merchant_id === $request->user()->merchant?->id, 403);

        $conversation->load('contact');
        $conversation->markAsRead();

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->latest()
            ->paginate(50);

        $orders = $conversation->orders()
            ->latest()
            ->get();

        return Inertia::render('Inbox/Show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'orders' => $orders,
        ]);
    }

    public function reply(Request $request, Conversation $conversation): RedirectResponse
    {
        abort_unless($conversation->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'direction' => 'outbound',
            'type' => 'text',
            'body' => $validated['body'],
            'status' => 'sent',
        ]);

        $conversation->updateLastMessage($message);

        // Dispatch job to send via platform API (IG/WA/FB)
        if ($conversation->platform !== 'manual') {
            \App\Jobs\SendOutboundMessage::dispatch($message, $conversation);
        }

        return back();
    }

    public function destroy(Request $request, Conversation $conversation): RedirectResponse
    {
        abort_unless($conversation->merchant_id === $request->user()->merchant?->id, 403);
        
        $conversation->messages()->delete();
        $conversation->delete();
        
        return redirect()->route('inbox.index')->with('success', 'Conversation deleted.');
    }
}
