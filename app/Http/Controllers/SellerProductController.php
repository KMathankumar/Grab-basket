<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class SellerProductController extends Controller
{
    /**
     * Show all products with search & filter.
     */
    public function index()
    {
        $query = Product::where('seller_id', auth('seller')->id())
            ->with('category', 'images'); // Load relationships

        // Search by product name
        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        // Filter by stock status
        if (request('stock_filter') == 'low_stock') {
            $query->where('stock', '<=', 5)->where('stock', '>', 0);
        } elseif (request('stock_filter') == 'out_of_stock') {
            $query->where('stock', 0);
        } elseif (request('stock_filter') == 'in_stock') {
            $query->where('stock', '>', 0);
        }

        $products = $query->latest()->paginate(6);

        return view('seller.my-products', compact('products'));
    }

    /**
     * Show form to create a new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('seller.add-product', compact('categories'));
    }

    /**
     * Store a new product with images and variants.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        // Create product
        $product = Product::create([
            'seller_id' => auth('seller')->id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_primary' => $index === 0, // First image is primary
                ]);
            }
        }

        // Handle variants
        if ($request->filled('variants')) {
            foreach ($request->variants as $variant) {
                if (!empty($variant['size']) || !empty($variant['color'])) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $variant['size'] ?? null,
                        'color' => $variant['color'] ?? null,
                        'price' => $variant['price'] ?? $product->price,
                        'stock' => $variant['stock'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('seller.products.index')->with('success', 'Product added successfully!');
    }

    /**
     * Show form to edit a product.
     */
    public function edit($id)
    {
        $product = Product::where('seller_id', auth('seller')->id())
            ->with('images', 'variants')
            ->findOrFail($id);

        $categories = Category::all();
        return view('seller.edit-product', compact('product', 'categories'));
    }

    /**
     * Update the product, images, and variants.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('seller_id', auth('seller')->id())
            ->with('images', 'variants')
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        // Update product
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'image' => $path,
                    'is_primary' => $product->images()->count() === 0, // First is primary
                ]);
            }
        }

        // Handle variants (simple update or replace)
        // For now: delete and re-create (you can enhance later)
        $product->variants()->delete();
        if ($request->filled('variants')) {
            foreach ($request->variants as $variant) {
                if (!empty($variant['size']) || !empty($variant['color'])) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $variant['size'] ?? null,
                        'color' => $variant['color'] ?? null,
                        'price' => $variant['price'] ?? $product->price,
                        'stock' => $variant['stock'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('seller.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete a product and its related data.
     */
    public function destroy($id)
    {
        $product = Product::where('seller_id', auth('seller')->id())->findOrFail($id);

      // Delete images from disk
foreach ($product->images as $image) {
    if (Storage::disk('public')->exists($image->image)) {
        Storage::disk('public')->delete($image->image);
    }
}

        // Delete from DB
        $product->images()->delete();
        $product->variants()->delete();
        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Product deleted successfully!');
    }

    /**
     * Export products to CSV.
     */
    public function export()
    {
        $products = Product::where('seller_id', auth('seller')->id())
            ->with('category', 'variants')
            ->get();

        $filename = "my-products-" . now()->format('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');

        // Header
        fputcsv($handle, [
            'Name', 'Category', 'Description', 'Price', 'Stock', 
            'Variants Count', 'Created At'
        ]);

        // Data
        foreach ($products as $product) {
            fputcsv($handle, [
                $product->name,
                $product->category?->name ?? 'Uncategorized',
                strip_tags($product->description),
                $product->price,
                $product->stock,
                $product->variants->count(),
                $product->created_at->format('M d, Y')
            ]);
        }

        fclose($handle);

        return response()->stream(
            function() use ($handle) {},
            200,
            [
                "Content-Type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
            ]
        );
    }
}