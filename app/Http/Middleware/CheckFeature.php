<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $feature
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $feature)
    {
        $user = $request->user();
        if (!$user || !$user->merchant) {
            abort(403, 'Unauthorized action.');
        }

        if (!$user->merchant->hasFeature($feature)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Feature locked. Upgrade required.'], 403);
            }
            return redirect()->route('subscription.index')
                ->with('error', 'The requested feature is locked on your current plan. Please upgrade to unlock.');
        }

        return $next($request);
    }
}
