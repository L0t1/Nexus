<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StorefrontController extends Controller
{
    /**
     * Public storefront page — no auth required.
     * Accessible at /shop/{slug}
     */
    public function show(string $slug)
    {
        $merchant = Merchant::where('business_slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::where('merchant_id', $merchant->id)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'price', 'image_path', 'stock']);

        return Inertia::render('Storefront/Show', [
            'merchant' => [
                'business_name'    => $merchant->business_name,
                'phone'            => $merchant->phone,
                'city'             => $merchant->city,
                'country'          => $merchant->country,
                'logo_path'        => $merchant->logo_path,
                'default_currency' => $merchant->default_currency ?? 'EUR',
            ],
            'products' => $products,
            'slug'     => $slug,
        ]);
    }

    /**
     * Merchant-facing storefront settings (embedded in Settings or standalone).
     */
    public function settings(Request $request)
    {
        $merchant = $request->user()->merchant;
        $products = $merchant->products()->where('is_active', true)->count();

        return Inertia::render('Storefront/Settings', [
            'storefrontUrl' => url("/shop/{$merchant->business_slug}"),
            'productCount'  => $products,
            'slug'          => $merchant->business_slug,
        ]);
    }
}
