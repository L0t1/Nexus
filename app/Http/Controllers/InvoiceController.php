<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function generate(Request $request, Order $order): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        abort_unless($order->merchant_id === $merchant->id, 403);

        // Check if invoice already exists
        $existing = Invoice::where('order_id', $order->id)->first();
        if ($existing) {
            return back()->with('success', "Invoice {$existing->invoice_number} already exists.");
        }

        $invoice = Invoice::create([
            'merchant_id' => $merchant->id,
            'order_id'    => $order->id,
            'subtotal'    => $order->subtotal,
            'tax_amount'  => $order->tax_amount ?? 0,
            'discount_amount' => $order->discount_amount ?? 0,
            'total'       => $order->total,
            'currency'    => $order->currency ?? $merchant->default_currency ?? 'EUR',
            'merchant_details' => [
                'name'    => $merchant->business_name,
                'phone'   => $merchant->phone,
                'email'   => $merchant->email,
                'address' => $merchant->address,
                'city'    => $merchant->city,
                'country' => $merchant->country,
            ],
            'customer_details' => [
                'name'  => $order->customer_name,
                'phone' => $order->customer_phone,
                'city'  => $order->shipping_city,
                'address' => $order->full_shipping_address,
            ],
            'issued_at' => now(),
            'due_at'    => now()->addDays(30),
        ]);

        ActivityLog::record($merchant->id, 'invoice.generated', "Invoice {$invoice->invoice_number} generated for order {$order->order_number}.", 'Invoice', $invoice->id, [
            'order' => $order->order_number
        ]);

        return back()->with('success', "Invoice {$invoice->invoice_number} generated.");
    }

    /**
     * Render invoice as an HTML page (printable/PDF-ready).
     */
    public function show(Request $request, Invoice $invoice): \Inertia\Response
    {
        abort_unless($invoice->merchant_id === $request->user()->merchant?->id, 403);
        $invoice->load('order');

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * List all invoices for the merchant.
     */
    public function index(Request $request): \Inertia\Response
    {
        $merchant = $request->user()->merchant;

        $invoices = Invoice::where('merchant_id', $merchant->id)
            ->with('order:id,order_number,customer_name')
            ->latest()
            ->paginate(25);

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }
}
