<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class WhatsAppCatalogController extends Controller
{
    /**
     * Dashboard showing catalog sync status.
     */
    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;
        $products = $merchant->products()->where('is_active', true)->get();

        $creds = $merchant->meta_credentials ?? [];
        $isConnected = !empty($creds['whatsapp_phone_id']) && !empty($creds['catalog_id']);

        return Inertia::render('WhatsAppCatalog/Index', [
            'products'    => $products,
            'isConnected' => $isConnected,
            'catalogId'   => $creds['catalog_id'] ?? null,
            'lastSyncAt'  => $creds['catalog_last_sync'] ?? null,
        ]);
    }

    /**
     * Sync all active products to WhatsApp Catalog via Meta Graph API.
     */
    public function sync(Request $request): JsonResponse
    {
        $merchant = $request->user()->merchant;
        $creds = $merchant->meta_credentials ?? [];

        $catalogId = $creds['catalog_id'] ?? null;
        $accessToken = $creds['access_token'] ?? null;

        if (!$catalogId || !$accessToken) {
            return response()->json([
                'success' => false,
                'error'   => 'WhatsApp catalog not configured. Please connect your WhatsApp Business account and set your Catalog ID in Settings.',
            ], 422);
        }

        $products = $merchant->products()->where('is_active', true)->get();
        $synced = 0;
        $errors = [];

        foreach ($products as $product) {
            try {
                $payload = [
                    'name'         => $product->name,
                    'description'  => $product->description ?: $product->name,
                    'price'        => (int) ($product->price * 100), // cents
                    'currency'     => $merchant->default_currency ?? 'EUR',
                    'availability' => $product->stock > 0 ? 'in stock' : 'out of stock',
                    'retailer_id'  => $product->sku ?: "nexus-{$product->id}",
                ];

                if ($product->image_url) {
                    $payload['image_url'] = $product->image_url;
                }

                // For demo/dev: simulate the API call
                if (config('app.env') === 'production') {
                    $response = Http::withToken($accessToken)
                        ->post("https://graph.facebook.com/v19.0/{$catalogId}/products", $payload);

                    if (!$response->successful()) {
                        $errors[] = "{$product->name}: " . ($response->json('error.message') ?? 'Unknown error');
                        continue;
                    }
                }

                $synced++;
            } catch (\Exception $e) {
                $errors[] = "{$product->name}: {$e->getMessage()}";
            }
        }

        // Update last sync timestamp
        $creds['catalog_last_sync'] = now()->toISOString();
        $merchant->update(['meta_credentials' => $creds]);

        ActivityLog::record($merchant->id, 'catalog.synced', "WhatsApp catalog synced: {$synced} products.", null, null, [
            'synced' => $synced,
            'errors' => count($errors),
        ]);

        return response()->json([
            'success' => true,
            'synced'  => $synced,
            'errors'  => $errors,
            'total'   => $products->count(),
        ]);
    }

    /**
     * Save catalog ID to merchant credentials.
     */
    public function saveCatalogId(Request $request)
    {
        $merchant = $request->user()->merchant;
        $validated = $request->validate(['catalog_id' => 'required|string|max:100']);

        $creds = $merchant->meta_credentials ?? [];
        $creds['catalog_id'] = $validated['catalog_id'];
        $merchant->update(['meta_credentials' => $creds]);

        return back()->with('success', 'Catalog ID saved.');
    }
}
