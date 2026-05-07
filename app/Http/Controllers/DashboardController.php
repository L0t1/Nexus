<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        if (!$merchant || !$merchant->onboarding_completed) {
            return Inertia::render('Onboarding');
        }

        // Single aggregated query replaces 11 separate DB round-trips.
        // Cached for 60 seconds per merchant to protect under rapid refreshes.
        $stats = Cache::remember("dashboard:stats:{$merchant->id}", 60, function () use ($merchant) {
            $todayStart = now()->startOfDay()->toDateTimeString();
            $monthStart = now()->startOfMonth()->toDateTimeString();

            $orderStats = $merchant->orders()
                ->selectRaw("
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as orders_today,
                    SUM(CASE WHEN fulfillment_status IN ('draft','confirmed','processing') THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN fulfillment_status IN ('dispatched','in_transit','out_for_delivery') THEN 1 ELSE 0 END) as in_transit,
                    SUM(CASE WHEN fulfillment_status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                    SUM(CASE WHEN created_at >= ? THEN total ELSE 0 END) as revenue_today,
                    SUM(CASE WHEN created_at >= ? THEN total ELSE 0 END) as revenue_month,
                    SUM(CASE WHEN payment_method = 'cod' AND payment_status = 'pending' AND fulfillment_status = 'delivered' THEN 1 ELSE 0 END) as cod_pending_count,
                    SUM(CASE WHEN payment_method = 'cod' AND payment_status = 'pending' AND fulfillment_status = 'delivered' THEN total ELSE 0 END) as cod_amount
                ", [$todayStart, $todayStart, $monthStart])
                ->first();

            $convStats = $merchant->conversations()
                ->selectRaw("
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_conversations,
                    SUM(CASE WHEN is_unread = true THEN 1 ELSE 0 END) as unread_conversations
                ")
                ->first();

            return [
                'total_orders'          => (int) $orderStats->total_orders,
                'orders_today'          => (int) $orderStats->orders_today,
                'pending_orders'        => (int) $orderStats->pending_orders,
                'in_transit'            => (int) $orderStats->in_transit,
                'delivered'             => (int) $orderStats->delivered,
                'revenue_today'         => round((float) $orderStats->revenue_today, 2),
                'revenue_month'         => round((float) $orderStats->revenue_month, 2),
                'active_conversations'  => (int) $convStats->active_conversations,
                'unread_conversations'  => (int) $convStats->unread_conversations,
                'total_contacts'        => $merchant->contacts()->count(),
                'cod_pending'           => (int) $orderStats->cod_pending_count,
                'cod_amount'            => round((float) $orderStats->cod_amount, 2),
            ];
        });

        $recentOrders = $merchant->orders()
            ->with('contact:id,name,phone')
            ->latest()
            ->take(10)
            ->get();

        $recentConversations = $merchant->conversations()
            ->with('contact:id,name,platform')
            ->latest('last_message_at')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats'               => $stats,
            'recentOrders'        => $recentOrders,
            'activeChats'         => $recentConversations,
        ]);
    }
}

