<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class TeamActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;
        $days = min((int) $request->input('days', 7), 90);

        // Guard: if the activity_logs table doesn't exist or is empty, render with defaults
        if (!Schema::hasTable('activity_logs')) {
            return $this->renderEmpty($days, $request);
        }

        try {
            $logs = ActivityLog::where('merchant_id', $merchant->id)
                ->with('user:id,name,email')
                ->when($request->input('user_id'), fn ($q, $uid) => $q->where('user_id', $uid))
                ->when($request->input('action'), fn ($q, $a) => $q->where('action', 'like', "{$a}%"))
                ->where('created_at', '>=', now()->subDays($days))
                ->latest()
                ->paginate(50);

            // Per-agent performance stats — SQLite-safe (DATE() works in both MySQL and SQLite)
            $isSqlite = config('database.default') === 'sqlite';

            $activeDaysExpr = $isSqlite
                ? "COUNT(DISTINCT strftime('%Y-%m-%d', created_at))"
                : "COUNT(DISTINCT DATE(created_at))";

            $agentRows = ActivityLog::where('merchant_id', $merchant->id)
                ->where('created_at', '>=', now()->subDays($days))
                ->whereNotNull('user_id')
                ->selectRaw("user_id, COUNT(*) as actions, {$activeDaysExpr} as active_days")
                ->groupBy('user_id')
                ->orderByDesc('actions')
                ->get();

            // Eager-load user names separately to avoid issues with selectRaw + with()
            $userIds = $agentRows->pluck('user_id')->unique()->filter();
            $userNames = DB::table('users')->whereIn('id', $userIds)->pluck('name', 'id');

            $agentStats = $agentRows->map(fn ($row) => [
                'user_id'     => $row->user_id,
                'name'        => $userNames[$row->user_id] ?? 'Unknown',
                'actions'     => (int) $row->actions,
                'active_days' => (int) $row->active_days,
            ]);

            // Action type breakdown — fully SQLite-compatible
            if ($isSqlite) {
                $actionTypes = ActivityLog::where('merchant_id', $merchant->id)
                    ->where('created_at', '>=', now()->subDays($days))
                    ->selectRaw("action, COUNT(*) as count")
                    ->groupBy('action')
                    ->pluck('count', 'action')
                    ->toArray();
            } else {
                $actionTypes = ActivityLog::where('merchant_id', $merchant->id)
                    ->where('created_at', '>=', now()->subDays($days))
                    ->selectRaw("SUBSTRING_INDEX(action, '.', 1) as category, COUNT(*) as count")
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray();
            }

            return Inertia::render('TeamActivity/Index', [
                'logs'        => $logs,
                'agentStats'  => $agentStats,
                'actionTypes' => $actionTypes,
                'days'        => $days,
                'filters'     => $request->only(['user_id', 'action', 'days']),
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $this->renderEmpty($days, $request);
        }
    }

    /**
     * Render the Team Activity page with empty data sets (safe fallback).
     */
    private function renderEmpty(int $days, Request $request): Response
    {
        return Inertia::render('TeamActivity/Index', [
            'logs'        => ['data' => [], 'links' => [], 'meta' => []],
            'agentStats'  => [],
            'actionTypes' => [],
            'days'        => $days,
            'filters'     => $request->only(['user_id', 'action', 'days']),
        ]);
    }
}
