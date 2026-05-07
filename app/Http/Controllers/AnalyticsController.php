<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;
        $days = min((int) $request->input('days', 30), 365); // cap at 1 year
        $startDate = now()->subDays($days);

        // ── Revenue chart — one DB query, zero PHP-level groupBy ──────────────
        $isPgsql = config('database.default') === 'pgsql';
        $dateExpr = $isPgsql ? "created_at::date" : "DATE(created_at)";

        $rawDailyData = $merchant->orders()
            ->where('created_at', '>=', $startDate)
            ->whereIn('fulfillment_status', ['confirmed', 'dispatched', 'in_transit', 'delivered'])
            ->selectRaw("{$dateExpr} as day, SUM(total) as revenue, COUNT(*) as orders")
            ->groupByRaw($dateExpr)
            ->orderBy('day')
            ->get()
            ->keyBy(fn ($row) => (string) $row->day);

        $chartData = collect();
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $key  = $date->format('Y-m-d');
            $row  = $rawDailyData->get($key);
            $chartData->push([
                'date'    => $date->format('M d'),
                'revenue' => $row ? round((float) $row->revenue, 2) : 0,
                'orders'  => $row ? (int) $row->orders : 0,
            ]);
        }

        // ── Summary stats — single aggregated query replaces 4 separate ones ──
        $summary = $merchant->orders()
            ->where('created_at', '>=', $startDate)
            ->selectRaw("
                COUNT(*) as total_orders,
                SUM(CASE WHEN fulfillment_status IN ('confirmed','dispatched','in_transit','delivered') THEN total ELSE 0 END) as total_revenue,
                SUM(CASE WHEN fulfillment_status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(CASE WHEN fulfillment_status = 'returned' THEN 1 ELSE 0 END) as returned_orders
            ")
            ->first();

        $totalOrders     = (int) $summary->total_orders;
        $totalRevenue    = (float) $summary->total_revenue;
        $deliveredOrders = (int) $summary->delivered_orders;
        $returnedOrders  = (int) $summary->returned_orders;

        $deliveryRate  = $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100, 1) : 0;
        $returnRate    = $totalOrders > 0 ? round(($returnedOrders / $totalOrders) * 100, 1) : 0;
        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // ── Status breakdown — GROUP BY instead of PHP groupBy ─────────────────
        $statusBreakdown = $merchant->orders()
            ->where('created_at', '>=', $startDate)
            ->selectRaw("fulfillment_status, COUNT(*) as count")
            ->groupBy('fulfillment_status')
            ->pluck('count', 'fulfillment_status')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        // ── Top cities — GROUP BY instead of PHP groupBy ───────────────────────
        $topCities = $merchant->orders()
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('shipping_city')
            ->selectRaw("shipping_city, COUNT(*) as orders, SUM(total) as revenue")
            ->groupBy('shipping_city')
            ->orderByDesc('orders')
            ->take(10)
            ->get()
            ->map(fn ($row) => [
                'city'    => $row->shipping_city,
                'orders'  => (int) $row->orders,
                'revenue' => (float) $row->revenue,
            ])
            ->values();

        // ── Channel breakdown — GROUP BY instead of PHP groupBy ───────────────
        $channelBreakdown = $merchant->conversations()
            ->where('created_at', '>=', $startDate)
            ->selectRaw("platform, COUNT(*) as count")
            ->groupBy('platform')
            ->pluck('count', 'platform')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        // ── Top Products (Cached for 1 hour to prevent loading all JSON items) ──
        $cacheKey = "merchant:{$merchant->id}:top_products:{$days}";
        $topProducts = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($merchant, $startDate) {
            $ordersWithItems = $merchant->orders()
                ->where('created_at', '>=', $startDate)
                ->whereNotNull('items')
                ->select('items')
                ->get();
                
            $productCounts = [];
            foreach ($ordersWithItems as $order) {
                foreach ($order->items as $item) {
                    $name = $item['name'] ?? 'Unknown';
                    $qty = (int)($item['qty'] ?? 1);
                    if (!isset($productCounts[$name])) {
                        $productCounts[$name] = ['name' => $name, 'sold' => 0];
                    }
                    $productCounts[$name]['sold'] += $qty;
                }
            }
            
            usort($productCounts, fn($a, $b) => $b['sold'] <=> $a['sold']);
            return array_slice($productCounts, 0, 5);
        });

        return Inertia::render('Analytics/Index', [
            'chartData' => $chartData,
            'summary' => [
                'total_revenue'    => round($totalRevenue, 2),
                'total_orders'     => $totalOrders,
                'delivered_orders' => $deliveredOrders,
                'returned_orders'  => $returnedOrders,
                'delivery_rate'    => $deliveryRate,
                'return_rate'      => $returnRate,
                'avg_order_value'  => $avgOrderValue,
            ],
            'statusBreakdown'  => $statusBreakdown,
            'topCities'        => $topCities,
            'channelBreakdown' => $channelBreakdown,
            'topProducts'      => $topProducts,
            'days'             => $days,
        ]);
    }
}
