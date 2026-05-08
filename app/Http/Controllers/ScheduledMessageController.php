<?php

namespace App\Http\Controllers;

use App\Models\ScheduledMessage;
use App\Models\FollowUpSequence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScheduledMessageController extends Controller
{
    /**
     * Schedule a message for future delivery.
     */
    public function store(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id,merchant_id,' . $merchant->id,
            'body'            => 'required|string|max:5000',
            'scheduled_at'    => 'required|date|after:now',
        ]);

        $merchant->scheduledMessages()->create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Message scheduled for ' . \Carbon\Carbon::parse($validated['scheduled_at'])->format('M d, H:i') . '.');
    }

    /**
     * Cancel a scheduled message.
     */
    public function destroy(Request $request, ScheduledMessage $scheduledMessage): RedirectResponse
    {
        abort_unless($scheduledMessage->merchant_id === $request->user()->merchant?->id, 403);
        abort_unless($scheduledMessage->status === 'pending', 422, 'Only pending messages can be cancelled.');

        $scheduledMessage->update(['status' => 'cancelled']);

        return back()->with('success', 'Scheduled message cancelled.');
    }

    /**
     * Follow-up sequences page — manage abandoned cart recovery.
     */
    public function followUps(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        if (!$merchant->hasFeature('ai_chat')) {
            return redirect('/subscription')->with('error', 'Follow-up sequences require the Ultimate plan.');
        }

        $sequences = $merchant->followUpSequences()
            ->withCount('logs')
            ->latest()
            ->get();

        // Pending scheduled messages
        $scheduledMessages = $merchant->scheduledMessages()
            ->where('status', 'pending')
            ->with(['conversation.contact:id,name,phone', 'creator:id,name'])
            ->orderBy('scheduled_at')
            ->take(20)
            ->get();

        return Inertia::render('FollowUps/Index', [
            'sequences'         => $sequences,
            'scheduledMessages' => $scheduledMessages,
        ]);
    }

    /**
     * Create a follow-up sequence.
     */
    public function storeSequence(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'message_template'  => 'required|string|max:2000',
            'delay_hours'       => 'required|integer|min:1|max:168', // max 1 week
            'trigger_condition' => 'required|in:no_order,no_reply',
        ]);

        $merchant->followUpSequences()->create($validated);

        return back()->with('success', "Follow-up sequence \"{$validated['name']}\" created.");
    }

    /**
     * Toggle a follow-up sequence on/off.
     */
    public function toggleSequence(Request $request, FollowUpSequence $sequence): RedirectResponse
    {
        abort_unless($sequence->merchant_id === $request->user()->merchant?->id, 403);

        $sequence->update(['is_active' => !$sequence->is_active]);

        $status = $sequence->is_active ? 'enabled' : 'paused';
        return back()->with('success', "Sequence \"{$sequence->name}\" {$status}.");
    }

    /**
     * Delete a follow-up sequence.
     */
    public function destroySequence(Request $request, FollowUpSequence $sequence): RedirectResponse
    {
        abort_unless($sequence->merchant_id === $request->user()->merchant?->id, 403);

        $sequence->logs()->delete();
        $sequence->delete();

        return back()->with('success', 'Sequence deleted.');
    }
}
