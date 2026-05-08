<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Export orders as CSV.
     */
    public function orders(Request $request): StreamedResponse
    {
        $merchant = $request->user()->merchant;

        $query = $merchant->orders()
            ->with('contact:id,name,phone')
            ->when($request->input('status'), fn ($q, $s) => $q->where('fulfillment_status', $s))
            ->when($request->input('payment'), fn ($q, $p) => $q->where('payment_status', $p))
            ->when($request->input('from'), fn ($q, $d) => $q->where('created_at', '>=', $d))
            ->when($request->input('to'), fn ($q, $d) => $q->where('created_at', '<=', $d))
            ->latest();

        return $this->streamCsv(
            "orders-" . now()->format('Y-m-d') . ".csv",
            ['Order #', 'Customer', 'Phone', 'City', 'Items', 'Subtotal', 'Shipping', 'Total', 'Payment Method', 'Payment Status', 'Fulfillment Status', 'Tracking #', 'Created'],
            $query,
            function (Order $order) {
                $items = collect($order->items ?? [])->map(fn ($i) => ($i['qty'] ?? 1) . 'x ' . ($i['name'] ?? 'Item'))->implode(', ');
                return [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_phone,
                    $order->shipping_city ?? '',
                    $items,
                    $order->subtotal,
                    $order->shipping_cost,
                    $order->total,
                    $order->payment_method,
                    $order->payment_status,
                    $order->fulfillment_status,
                    $order->tracking_number ?? '',
                    $order->created_at->format('Y-m-d H:i'),
                ];
            }
        );
    }

    /**
     * Export contacts as CSV.
     */
    public function contacts(Request $request): StreamedResponse
    {
        $merchant = $request->user()->merchant;

        $query = $merchant->contacts()
            ->with('tags:id,name')
            ->when($request->input('platform'), fn ($q, $p) => $q->where('platform', $p))
            ->when($request->input('tag'), function ($q, $tagId) {
                $q->whereHas('tags', fn ($t) => $t->where('tags.id', $tagId));
            })
            ->latest();

        return $this->streamCsv(
            "contacts-" . now()->format('Y-m-d') . ".csv",
            ['Name', 'Phone', 'Email', 'Platform', 'City', 'Neighborhood', 'Street', 'Tags', 'Total Orders', 'Total Spent', 'Notes', 'Created'],
            $query,
            function (Contact $contact) {
                return [
                    $contact->name ?? '',
                    $contact->phone ?? '',
                    $contact->email ?? '',
                    $contact->platform ?? '',
                    $contact->city ?? '',
                    $contact->neighborhood ?? '',
                    $contact->street_address ?? '',
                    $contact->tags->pluck('name')->implode(', '),
                    $contact->total_orders,
                    $contact->total_spent,
                    $contact->notes ?? '',
                    $contact->created_at->format('Y-m-d H:i'),
                ];
            }
        );
    }

    /**
     * Export products as CSV.
     */
    public function products(Request $request): StreamedResponse
    {
        $merchant = $request->user()->merchant;

        $query = $merchant->products()->latest();

        return $this->streamCsv(
            "products-" . now()->format('Y-m-d') . ".csv",
            ['Name', 'SKU', 'Description', 'Price', 'Stock', 'Active', 'Created'],
            $query,
            function (Product $product) {
                return [
                    $product->name,
                    $product->sku ?? '',
                    $product->description ?? '',
                    $product->price,
                    $product->stock,
                    $product->is_active ? 'Yes' : 'No',
                    $product->created_at->format('Y-m-d H:i'),
                ];
            }
        );
    }

    /**
     * Generic CSV streaming helper. Chunks 500 at a time to avoid memory issues.
     */
    private function streamCsv(string $filename, array $headers, $query, callable $rowMapper): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $query, $rowMapper) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            $query->chunk(500, function ($rows) use ($handle, $rowMapper) {
                foreach ($rows as $row) {
                    fputcsv($handle, $rowMapper($row));
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
