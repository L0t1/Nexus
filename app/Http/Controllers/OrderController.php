<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Courier\CourierServiceInterface;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private NotificationService $notifications
    ) {}

    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        $orders = $merchant->orders()
            ->with('contact:id,name,phone')
            ->when($request->input('status'), fn ($q, $s) => $q->where('fulfillment_status', $s))
            ->when($request->input('payment'), fn ($q, $p) => $q->where('payment_status', $p))
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('tracking_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(25);

        $rawCounts = $merchant->orders()
            ->selectRaw('fulfillment_status, COUNT(*) as count')
            ->groupBy('fulfillment_status')
            ->pluck('count', 'fulfillment_status');

        $statusCounts = [
            'all'        => $rawCounts->sum(),
            'draft'      => (int) $rawCounts->get('draft', 0),
            'confirmed'  => (int) $rawCounts->get('confirmed', 0),
            'dispatched' => (int) $rawCounts->get('dispatched', 0),
            'delivered'  => (int) $rawCounts->get('delivered', 0),
            'returned'   => (int) $rawCounts->get('returned', 0),
        ];

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'filters' => $request->only(['status', 'payment', 'search']),
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        abort_unless($order->merchant_id === $request->user()->merchant?->id, 403);

        $order->load(['contact', 'conversation', 'creator:id,name']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $rules = [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['required', 'string', 'regex:/^[\d\s\+\-\(\)]+$/', 'max:50'],
            'shipping_city' => 'required|string|max:255',
            'shipping_neighborhood' => 'nullable|string|max:255',
            'shipping_street' => 'required|string|max:500',
            'shipping_notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0.01',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'shipping_cost' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cod,bank_transfer,online',
            'contact_id'      => 'nullable|exists:contacts,id,merchant_id,' . $merchant->id,
            'conversation_id'  => 'nullable|exists:conversations,id,merchant_id,' . $merchant->id,
            'internal_notes' => 'nullable|string|max:2000',
        ];

        $messages = [
            'customer_name.required' => 'Customer name is required.',
            'customer_name.max' => 'Name is too long.',
            'customer_phone.required' => 'Phone number is required.',
            'customer_phone.regex' => 'Please enter a valid phone number (digits, +, - allowed).',
            'shipping_city.required' => 'City is required.',
            'shipping_street.required' => 'Address is required.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method selected.',
            'items.*.name.required' => 'Product name is required.',
            'items.*.qty.required' => 'Quantity is required.',
            'items.*.qty.min' => 'Quantity must be at least 1.',
            'items.*.price.required' => 'Price is required.',
            'items.*.price.numeric' => 'Price must be a number.',
            'items.*.price.min' => 'Price cannot be negative or zero.',
            'shipping_cost.numeric' => 'Shipping cost must be a number.',
            'shipping_cost.min' => 'Shipping cost cannot be negative.',
        ];

        $validated = $request->validate($rules, $messages);

        // Enforce subscription order limit
        $limit = $merchant->order_limit;
        if ($limit !== null && $merchant->orders()->count() >= $limit) {
            return back()->withErrors([
                'limit' => "You have reached your plan limit of {$limit} orders. Please upgrade to the Pro plan.",
            ]);
        }

        $order = $merchant->orders()->create([
            ...$validated,
            'created_by' => $request->user()->id,
            'fulfillment_status' => 'confirmed',
        ]);

        $order->calculateTotal();
        $order->save();

        if ($order->contact) {
            $order->contact->incrementOrderStats($order->total);
        }

        // Send confirmation notification
        $this->notifications->sendOrderNotification($order, 'confirmed');

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order #' . $order->order_number . ' created successfully.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'fulfillment_status' => 'nullable|in:draft,confirmed,processing,dispatched,in_transit,out_for_delivery,delivered,returned,cancelled',
            'payment_status'     => 'nullable|in:pending,collected,returned,refunded',
        ]);

        $updates = $validated;

        $oldStatus = $order->fulfillment_status;

        if (isset($validated['fulfillment_status'])) {
            if ($validated['fulfillment_status'] === 'dispatched') {
                $updates['dispatched_at'] = now();
            } elseif ($validated['fulfillment_status'] === 'delivered') {
                $updates['delivered_at'] = now();
                $updates['payment_status'] = 'collected';
            } elseif (in_array($validated['fulfillment_status'], ['cancelled', 'returned'])) {
                $updates['payment_status'] = 'returned';
            }
        }

        $order->update($updates);
        $newStatus = $order->fulfillment_status;

        // Auto-Stock Deduction
        if ($oldStatus !== 'delivered' && $newStatus === 'delivered') {
            $this->adjustStock($order, -1);
        } elseif ($oldStatus === 'delivered' && in_array($newStatus, ['returned', 'cancelled'])) {
            $this->adjustStock($order, 1);
        }

        // Send notification for key status changes
        $notifyStatuses = ['confirmed', 'dispatched', 'out_for_delivery', 'delivered'];
        if (isset($validated['fulfillment_status']) && in_array($validated['fulfillment_status'], $notifyStatuses)) {
            $this->notifications->sendOrderNotification($order, $validated['fulfillment_status']);
        }

        return back()->with('success', 'Order status updated.');
    }

    private function adjustStock(Order $order, int $multiplier): void
    {
        $items = $order->items ?? [];
        foreach ($items as $item) {
            $product = null;
            if (!empty($item['product_id'])) {
                $product = $order->merchant->products()->find($item['product_id']);
            } else {
                $product = $order->merchant->products()->where('name', $item['name'])->first();
            }

            if ($product) {
                // If multiplier is -1, we subtract stock. If 1, we add.
                $product->increment('stock', $item['qty'] * $multiplier);
            }
        }
    }

    public function dispatch(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->merchant_id === $request->user()->merchant?->id, 403);
        abort_unless($order->canBeDispatched(), 422, 'Order cannot be dispatched in current state.');

        $courier = app(CourierServiceInterface::class);
        $result = $courier->createShipment($order);

        $this->notifications->sendOrderNotification($order->fresh(), 'dispatched');

        return back()->with('success', "Order dispatched. Tracking: {$result['tracking_number']}");
    }

    public function destroy(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->merchant_id === $request->user()->merchant?->id, 403);

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
}
