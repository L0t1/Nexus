<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;
        $days = min((int) $request->input('days', 7), 90);

        $logs = ActivityLog::where('merchant_id', $merchant->id)
            ->with('user:id,name,email')
            ->when($request->input('user_id'), fn ($q, $uid) => $q->where('user_id', $uid))
            ->when($request->input('action'), fn ($q, $a) => $q->where('action', 'like', "{$a}%"))
            ->where('created_at', '>=', now()->subDays($days))
            ->latest()
            ->paginate(50);

        // Per-agent performance stats
        $agentStats = ActivityLog::where('merchant_id', $merchant->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('user_id')
            ->selectRaw('user_id, COUNT(*) as actions, COUNT(DISTINCT DATE(created_at)) as active_days')
            ->groupBy('user_id')
            ->with('user:id,name')
            ->orderByDesc('actions')
            ->get()
            ->map(fn ($row) => [
                'user_id'     => $row->user_id,
                'name'        => $row->user?->name ?? 'Unknown',
                'actions'     => (int) $row->actions,
                'active_days' => (int) $row->active_days,
            ]);

        // Action type breakdown
        $actionTypes = ActivityLog::where('merchant_id', $merchant->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw("SUBSTRING_INDEX(action, '.', 1) as category, COUNT(*) as count")
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Use simpler grouping for SQLite
        if (config('database.default') === 'sqlite') {
            $actionTypes = ActivityLog::where('merchant_id', $merchant->id)
                ->where('created_at', '>=', now()->subDays($days))
                ->selectRaw("action, COUNT(*) as count")
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray();
        }

        return Inertia::render('TeamActivity/Index', [
            'logs'        => $logs,
            'agentStats'  => $agentStats,
            'actionTypes' => $actionTypes,
            'days'        => $days,
            'filters'     => $request->only(['user_id', 'action', 'days']),
        ]);
    }
}
