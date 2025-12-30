<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('variants')->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_buy' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0', // This is price_sell input
            'variants' => 'required|array|min:1',
            'variants.*.color' => 'required|string',
            'variants.*.size' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Upload Image if exists
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('products', 'public');
                }

                // 1. Create Product
                $product = Product::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price_sell' => $request->price,
                    'price_buy' => $request->price_buy,
                    'image' => $imagePath,
                ]);

                // 2. Create Variants
                foreach ($request->variants as $variantData) {
                    $product->variants()->create([
                        'color' => $variantData['color'],
                        'size' => $variantData['size'],
                    ]);
                }
            });

            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load('variants');
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_buy' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:variants,id',
            'variants.*.color' => 'required|string',
            'variants.*.size' => 'required|string',
            'variants.*._delete' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {
                // Handle Image Upload
                if ($request->hasFile('image')) {
                    // Delete old image
                    if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
                    }
                    $imagePath = $request->file('image')->store('products', 'public');
                } else {
                    $imagePath = $product->image;
                }

                // 1. Update Product
                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price_sell' => $request->price,
                    'price_buy' => $request->price_buy,
                    'image' => $imagePath,
                ]);

                // 2. Sync Variants
                if ($request->has('variants')) {
                    foreach ($request->variants as $variantData) {
                        if (!empty($variantData['_delete']) && !empty($variantData['id'])) {
                            // Delete existing variant
                            Variant::destroy($variantData['id']);
                        } elseif (!empty($variantData['id'])) {
                            // Update existing variant
                            Variant::where('id', $variantData['id'])->update([
                                'color' => $variantData['color'],
                                'size' => $variantData['size'],
                            ]);
                        } else {
                            // Create new variant (ignore if marked for delete before save)
                            if (empty($variantData['_delete'])) {
                                $product->variants()->create([
                                    'color' => $variantData['color'],
                                    'size' => $variantData['size'],
                                ]);
                            }
                        }
                    }
                }
            });

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product. It might be in use.');
        }
    }
}
