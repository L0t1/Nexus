<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;
        $coupons = Coupon::where('merchant_id', $merchant->id)
            ->withCount('usages')
            ->latest()
            ->paginate(25);

        return Inertia::render('Coupons/Index', ['coupons' => $coupons]);
    }

    public function store(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;
        $validated = $request->validate([
            'code'               => 'required|string|max:50|alpha_dash|unique:coupons,code,NULL,id,merchant_id,' . $merchant->id,
            'name'               => 'required|string|max:100',
            'type'               => 'required|in:percentage,fixed',
            'value'              => 'required|numeric|min:0.01',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_discount_amount'=> 'nullable|numeric|min:0',
            'usage_limit'        => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'starts_at'          => 'nullable|date',
            'expires_at'         => 'nullable|date|after:now',
        ]);

        if (empty($validated['starts_at'])) {
            $validated['starts_at'] = now();
        }

        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = \Illuminate\Support\Carbon::parse($validated['expires_at'])->endOfDay();
        }

        $validated['code'] = strtoupper($validated['code']);
        $coupon = $merchant->coupons()->create($validated);

        ActivityLog::record($merchant->id, 'coupon.created', "Coupon {$coupon->code} created.", 'Coupon', $coupon->id, [
            'code' => $coupon->code
        ]);
        return back()->with('success', "Coupon \"{$coupon->code}\" created.");
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        abort_unless($coupon->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'type'               => 'required|in:percentage,fixed',
            'value'              => 'required|numeric|min:0.01',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_discount_amount'=> 'nullable|numeric|min:0',
            'usage_limit'        => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'is_active'          => 'boolean',
            'starts_at'          => 'nullable|date',
            'expires_at'         => 'nullable|date',
        ]);

        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = \Illuminate\Support\Carbon::parse($validated['expires_at'])->endOfDay();
        }

        $coupon->update($validated);
        return back()->with('success', 'Coupon updated.');
    }

    public function destroy(Request $request, Coupon $coupon): RedirectResponse
    {
        abort_unless($coupon->merchant_id === $request->user()->merchant?->id, 403);
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }

    /**
     * Validate a coupon code (AJAX — used from order form).
     */
    public function validate_code(Request $request): JsonResponse
    {
        $merchant = $request->user()->merchant;
        $code = strtoupper($request->input('code', ''));
        $subtotal = (float) $request->input('subtotal', 0);

        $coupon = Coupon::where('merchant_id', $merchant->id)->where('code', $code)->first();

        if (!$coupon) return response()->json(['valid' => false, 'error' => 'Coupon not found.']);
        if (!$coupon->isValid()) return response()->json(['valid' => false, 'error' => 'Coupon is expired or inactive.']);

        $discount = $coupon->calculateDiscount($subtotal);
        if ($discount <= 0) return response()->json(['valid' => false, 'error' => "Minimum order €{$coupon->min_order_amount} required."]);

        return response()->json([
            'valid'    => true,
            'coupon_id' => $coupon->id,
            'discount' => $discount,
            'label'    => $coupon->type === 'percentage' ? "{$coupon->value}% off" : "€{$coupon->value} off",
        ]);
    }
}
