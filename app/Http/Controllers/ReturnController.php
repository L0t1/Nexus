<?php

namespace App\Http\Controllers;

use App\Models\OrderReturn;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReturnController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        $returns = OrderReturn::where('merchant_id', $merchant->id)
            ->with(['order:id,order_number,customer_name,total', 'handler:id,name'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(25);

        $statusCounts = OrderReturn::where('merchant_id', $merchant->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return Inertia::render('Returns/Index', [
            'returns'      => $returns,
            'statusCounts' => $statusCounts,
            'filters'      => $request->only('status'),
        ]);
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        abort_unless($order->merchant_id === $merchant->id, 403);

        // Prevent duplicate returns for same order
        if (OrderReturn::where('order_id', $order->id)->exists()) {
            return back()->withErrors(['order' => 'A return request already exists for this order.']);
        }

        $validated = $request->validate([
            'reason'      => 'required|in:defective,wrong_item,not_as_described,changed_mind,late_delivery,other',
            'description' => 'nullable|string|max:2000',
            'items'       => 'nullable|array',
        ]);

        $return = OrderReturn::create([
            'merchant_id' => $merchant->id,
            'order_id'    => $order->id,
            'contact_id'  => $order->contact_id,
            'handled_by'  => $request->user()->id,
            'reason'      => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'items'       => $validated['items'] ?? $order->items,
        ]);

        OrderEvent::log($order, 'return_requested', "Return {$return->return_number} requested: {$validated['reason']}.", ['return_id' => $return->id]);
        ActivityLog::record($merchant->id, 'return.created', "Return {$return->return_number} created for order {$order->order_number}.", 'OrderReturn', $return->id, [
            'order' => $order->order_number
        ]);

        return back()->with('success', "Return {$return->return_number} created.");
    }

    public function updateStatus(Request $request, OrderReturn $return): RedirectResponse
    {
        abort_unless($return->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'status'        => 'required|in:approved,rejected,received,refunded,exchanged',
            'refund_method' => 'nullable|in:original,store_credit,bank_transfer,cash',
            'refund_amount' => 'nullable|numeric|min:0.01',
            'admin_notes'   => 'nullable|string|max:2000',
        ]);

        $updates = ['status' => $validated['status']];
        if (isset($validated['admin_notes'])) $updates['admin_notes'] = $validated['admin_notes'];
        if (isset($validated['refund_method'])) $updates['refund_method'] = $validated['refund_method'];
        if (isset($validated['refund_amount'])) $updates['refund_amount'] = $validated['refund_amount'];

        if ($validated['status'] === 'approved') $updates['approved_at'] = now();
        if ($validated['status'] === 'received') $updates['received_at'] = now();
        if ($validated['status'] === 'refunded') {
            $updates['refunded_at'] = now();
            // Update order payment status
            $return->order->update(['payment_status' => 'refunded']);
            OrderEvent::log($return->order, 'refunded', "Order refunded via return {$return->return_number}. Amount: €{$validated['refund_amount']}.");
        }

        $return->update($updates);

        ActivityLog::record($return->merchant_id, 'return.status_changed', "Return {$return->return_number} status changed to {$validated['status']}.", 'OrderReturn', $return->id, [
            'order' => $return->order->order_number,
            'status' => $validated['status']
        ]);
        return back()->with('success', "Return status updated to {$validated['status']}.");
    }
}
