<?php

namespace App\Http\Controllers;

use App\Models\DemandForecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class ForecastController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        // Generate/refresh forecasts
        $this->generateForecasts($merchant);

        $forecasts = DemandForecast::where('merchant_id', $merchant->id)
            ->where('forecast_date', '>=', now()->startOfDay())
            ->orderBy('forecast_date')
            ->orderByDesc('predicted_demand')
            ->get()
            ->groupBy('product_name');

        // Historical sales data (last 30 days per product)
        $cacheKey = "merchant:{$merchant->id}:sales_history";
        $salesHistory = Cache::remember($cacheKey, 3600, function () use ($merchant) {
            $orders = $merchant->orders()
                ->where('created_at', '>=', now()->subDays(90))
                ->whereIn('fulfillment_status', ['confirmed', 'dispatched', 'delivered'])
                ->select('items', 'created_at')
                ->get();

            $daily = [];
            foreach ($orders as $order) {
                $date = $order->created_at->format('Y-m-d');
                foreach ($order->items ?? [] as $item) {
                    $name = $item['name'] ?? 'Unknown';
                    $daily[$name][$date] = ($daily[$name][$date] ?? 0) + ($item['qty'] ?? 1);
                }
            }

            return $daily;
        });

        // Low stock alerts
        $lowStockProducts = $merchant->products()
            ->where('is_active', true)
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->get(['id', 'name', 'stock', 'sku']);

        // Restock recommendations
        $restockRecommendations = [];
        foreach ($forecasts as $productName => $productForecasts) {
            $next7Days = $productForecasts->where('forecast_date', '<=', now()->addDays(7));
            $totalDemand = $next7Days->sum('predicted_demand');
            $product = $merchant->products()->where('name', $productName)->first();
            $currentStock = $product ? $product->stock : 0;

            if ($totalDemand > $currentStock) {
                $restockRecommendations[] = [
                    'product'       => $productName,
                    'current_stock' => $currentStock,
                    'predicted_7d'  => $totalDemand,
                    'shortfall'     => $totalDemand - $currentStock,
                    'trend'         => $next7Days->first()?->trend ?? 'stable',
                ];
            }
        }

        return Inertia::render('Forecast/Index', [
            'forecasts'              => $forecasts,
            'salesHistory'           => $salesHistory,
            'lowStockProducts'       => $lowStockProducts,
            'restockRecommendations' => $restockRecommendations,
        ]);
    }

    /**
     * Simple linear regression-based demand forecasting.
     * Uses the last 30 days of sales data to predict next 14 days.
     */
    private function generateForecasts($merchant): void
    {
        $cacheKey = "merchant:{$merchant->id}:forecasts_generated";
        if (Cache::has($cacheKey)) return; // Already generated today

        // Get sales data for last 60 days
        $orders = $merchant->orders()
            ->where('created_at', '>=', now()->subDays(60))
            ->whereIn('fulfillment_status', ['confirmed', 'dispatched', 'delivered'])
            ->select('items', 'created_at')
            ->get();

        $productDailySales = [];
        foreach ($orders as $order) {
            $date = $order->created_at->format('Y-m-d');
            foreach ($order->items ?? [] as $item) {
                $name = $item['name'] ?? 'Unknown';
                $productDailySales[$name][$date] = ($productDailySales[$name][$date] ?? 0) + ($item['qty'] ?? 1);
            }
        }

        foreach ($productDailySales as $productName => $dailySales) {
            // Fill missing days with 0
            $sales = [];
            for ($i = 59; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $sales[] = $dailySales[$date] ?? 0;
            }

            // Simple moving average (7-day window)
            $recentAvg = count($sales) >= 7 ? array_sum(array_slice($sales, -7)) / 7 : (array_sum($sales) / max(count($sales), 1));

            // Trend: compare last 7 days avg vs previous 7 days avg
            $last7 = array_sum(array_slice($sales, -7)) / 7;
            $prev7 = array_sum(array_slice($sales, -14, 7)) / 7;
            $trend = $last7 > $prev7 * 1.1 ? 'rising' : ($last7 < $prev7 * 0.9 ? 'falling' : 'stable');

            // Generate 14-day forecast
            for ($d = 0; $d < 14; $d++) {
                $forecastDate = now()->addDays($d)->format('Y-m-d');
                $dayOfWeek = now()->addDays($d)->dayOfWeek;

                // Weekend adjustment (usually lower)
                $weekendFactor = in_array($dayOfWeek, [0, 6]) ? 0.7 : 1.0;
                $trendFactor = $trend === 'rising' ? 1 + ($d * 0.02) : ($trend === 'falling' ? 1 - ($d * 0.01) : 1.0);

                $predicted = max(0, round($recentAvg * $weekendFactor * $trendFactor));
                $confidence = max(30, min(95, 85 - ($d * 3))); // Confidence decreases with distance

                DemandForecast::updateOrCreate(
                    ['merchant_id' => $merchant->id, 'product_name' => $productName, 'forecast_date' => $forecastDate],
                    [
                        'product_id'       => $merchant->products()->where('name', $productName)->value('id'),
                        'predicted_demand' => $predicted,
                        'confidence'       => $confidence,
                        'trend'            => $trend,
                        'factors'          => ['method' => 'moving_average_7d', 'weekend_adjusted' => in_array($dayOfWeek, [0, 6])],
                    ]
                );
            }
        }

        Cache::put($cacheKey, true, now()->endOfDay());
    }
}
