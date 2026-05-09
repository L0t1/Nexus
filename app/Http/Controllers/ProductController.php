<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }
        unset($validated['image']);

        $product = $request->user()->merchant->products()->create($validated);
        \App\Models\ActivityLog::record($request->user()->merchant_id, 'product.created', "Created product: {$product->name}.", 'Product', $product->id, ['name' => $product->name]);

        return redirect()->back()->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }
        unset($validated['image']);

        $product->update($validated);
        \App\Models\ActivityLog::record($request->user()->merchant_id, 'product.updated', "Updated product: {$product->name}.", 'Product', $product->id, ['name' => $product->name]);

        return redirect()->back()->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($product->merchant_id === $request->user()->merchant?->id, 403);
        
        \App\Models\ActivityLog::record($request->user()->merchant_id, 'product.deleted', "Deleted product: {$product->name}.", 'Product', $product->id, ['name' => $product->name]);
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }
}
