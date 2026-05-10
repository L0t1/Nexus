<?php

namespace App\Http\Controllers;

use App\Models\CsatSurvey;
use App\Models\Order;
use App\Models\Message;
use App\Models\ActivityLog;
use App\Jobs\SendOutboundMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CsatController extends Controller
{
    /**
     * Dashboard showing CSAT analytics.
     */
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;
        $days = min((int) $request->input('days', 30), 365);

        $surveys = CsatSurvey::where('merchant_id', $merchant->id)
            ->with(['order:id,order_number,customer_name', 'contact:id,name'])
            ->latest()
            ->paginate(25);

        // Stats
        $completed = CsatSurvey::where('merchant_id', $merchant->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($days));

        $avgRating    = round($completed->avg('rating') ?? 0, 1);
        $totalRatings = $completed->count();
        $distribution = CsatSurvey::where('merchant_id', $merchant->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // NPS-style calculation: promoters (4-5) - detractors (1-2) / total
        $promoters  = ($distribution[5] ?? 0) + ($distribution[4] ?? 0);
        $detractors = ($distribution[1] ?? 0) + ($distribution[2] ?? 0);
        $nps = $totalRatings > 0 ? round((($promoters - $detractors) / $totalRatings) * 100) : 0;

        return Inertia::render('Csat/Index', [
            'surveys'      => $surveys,
            'avgRating'    => $avgRating,
            'totalRatings' => $totalRatings,
            'distribution' => $distribution,
            'nps'          => $nps,
            'days'         => $days,
        ]);
    }

    /**
     * Send CSAT survey for a specific order.
     */
    public function send(Request $request, Order $order): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        abort_unless($order->merchant_id === $merchant->id, 403);

        // Ensure order is delivered before sending CSAT
        if ($order->fulfillment_status !== 'delivered') {
            return back()->with('error', 'CSAT surveys can only be sent for delivered orders.');
        }

        $existing = CsatSurvey::where('order_id', $order->id)->first();
        if ($existing) {
            $survey = $existing;
        } else {
            $survey = CsatSurvey::create([
                'merchant_id'     => $merchant->id,
                'order_id'        => $order->id,
                'contact_id'      => $order->contact_id,
                'conversation_id' => $order->conversation_id,
                'sent_at'         => now(),
                'token'           => Str::random(32),
                'status'          => 'pending',
            ]);
        }

        $link = config('app.public_url', config('app.url')) . "/feedback/{$survey->token}";

        // AUTOMATION: Send link to customer if conversation exists
        if ($order->conversation_id) {
            $conversation = $order->conversation;
            
            // Determine language (default to Albanian if merchant city/country suggests it, or from settings)
            $locale = $merchant->settings['language'] ?? ($merchant->country === 'Albania' ? 'sq' : 'en');
            
            $body = $locale === 'sq' 
                ? "Faleminderit që zgjodhët {$merchant->business_name}! Do të vlerësonim shumë reagimin tuaj për porosinë tuaj të fundit: {$link}"
                : "Thank you for choosing {$merchant->business_name}! We would greatly appreciate your feedback on your recent order: {$link}";

            $message = Message::create([
                'merchant_id' => $merchant->id,
                'conversation_id' => $conversation->id,
                'contact_id' => $conversation->contact_id,
                'direction' => 'outbound',
                'body' => $body,
                'type' => 'text',
                'status' => 'queued',
            ]);

            \App\Jobs\SendOutboundMessage::dispatch($message, $conversation);
        }

        ActivityLog::record($merchant->id, 'csat.sent', "CSAT survey sent for order {$order->order_number}.", 'CsatSurvey', $survey->id, [
            'order' => $order->order_number
        ]);
        
        return back()->with([
            'success' => 'CSAT survey generated and sent to customer.',
            'csat_link' => $link
        ]);
    }

    /**
     * Public feedback page (no auth required).
     */
    public function feedback(string $token)
    {
        $survey = CsatSurvey::where('token', $token)->firstOrFail();
        $survey->load('order:id,order_number', 'merchant:id,business_name');

        return Inertia::render('Csat/Feedback', [
            'survey' => $survey,
        ]);
    }

    /**
     * Submit feedback (public, no auth).
     */
    public function submitFeedback(Request $request, string $token): RedirectResponse
    {
        $survey = CsatSurvey::where('token', $token)->where('status', 'pending')->firstOrFail();

        $validated = $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:2000',
        ]);

        $survey->update([
            ...$validated,
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }
}
