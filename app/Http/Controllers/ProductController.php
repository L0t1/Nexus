<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->merchant->hasFeature('products')) {
            return redirect('/subscription')->with('error', 'Products are locked. Upgrade your plan to access this feature!');
        }

        $products = $request->user()->merchant->products()
            ->latest()
            ->paginate(20);

        return Inertia::render('Products/Index', [
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $request->user()->merchant->products()->create($validated);

        return redirect()->back()->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->merchant_id === $request->user()->merchant_id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->back()->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($product->merchant_id === $request->user()->merchant_id, 403);
        
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }
}
