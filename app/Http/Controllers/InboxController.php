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

        $hasPriorityInbox = $merchant->hasFeature('priority_inbox');

        $query = $merchant->conversations()
            ->with(['contact:id,name,phone,platform,city', 'assignee:id,name'])
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->input('platform'), fn ($q, $platform) => $q->forPlatform($platform))
            ->when($request->input('search'), function ($q, $search) {
                $q->whereHas('contact', fn ($c) => $c->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"));
            });

        if ($hasPriorityInbox) {
            $query->withCount(['orders' => fn($q) => $q->where('fulfillment_status', 'delivered')])
                  ->orderByDesc('orders_count')
                  ->orderByDesc('last_message_at');
        } else {
            $query->latest('last_message_at');
        }

        $conversations = $query->paginate(30);

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

        // Pass available products so the order form can offer product selection
        $products = $request->user()->merchant->products()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'stock', 'sku']);

        // Quick reply templates
        $templates = $request->user()->merchant->replyTemplates()
            ->orderByDesc('usage_count')
            ->get(['id', 'shortcut', 'name', 'body', 'category']);

        // Pending scheduled messages for this conversation
        $scheduledMessages = $request->user()->merchant->scheduledMessages()
            ->where('conversation_id', $conversation->id)
            ->where('status', 'pending')
            ->orderBy('scheduled_at')
            ->get();

        return Inertia::render('Inbox/Show', [
            'conversation'      => $conversation,
            'messages'          => $messages,
            'orders'            => $orders,
            'products'          => $products,
            'templates'         => $templates,
            'scheduledMessages' => $scheduledMessages,
        ]);
    }

    public function reply(Request $request, Conversation $conversation): RedirectResponse
    {
        abort_unless($conversation->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'body' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        // Determine message type and media
        $type = 'text';
        $media = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat-images', 'public');
            $type = 'image';
            $media = ['url' => asset('storage/' . $path), 'path' => $path];
        }

        // Must have either text or image
        if (empty($validated['body']) && !$media) {
            return back();
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'direction' => 'outbound',
            'type' => $type,
            'body' => $validated['body'] ?? null,
            'media' => $media,
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
