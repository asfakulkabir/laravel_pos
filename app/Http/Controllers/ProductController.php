<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Color;
use App\Models\Size;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = \App\Models\Product::with(['category', 'colors.color', 'colors.sizes.size'])->latest()->get();
        // Calculate total stock for each product
        $products->each(function ($product) {
            $product->total_stock = $product->colors->sum(function($pColor) {
                return $pColor->sizes->sum('stock');
            });
        });

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized action.');
        
        $categories = \App\Models\Category::with('subcategories')->get();
        $colors = \App\Models\Color::all();
        $sizes = \App\Models\Size::all();
        
        return view('products.create', compact('categories', 'colors', 'sizes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:sub_categories,id',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'costing_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.color_id' => 'required|exists:colors,id',
            'variants.*.box_number' => 'nullable|string|max:255',
            'variants.*.sizes' => 'required|array|min:1',
            'variants.*.sizes.*.size_id' => 'required|exists:sizes,id',
            'variants.*.sizes.*.stock' => 'required|integer|min:0',
            'variants.*.sizes.*.box_number' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            // Create Product
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'name' => $validated['name'],
                'type' => $validated['type'] ?? null,
                'selling_price' => $validated['selling_price'],
                'costing_price' => $validated['costing_price'] ?? 0,
                'description' => $validated['description'],
                // sku, slug, is_active auto-handled by Model
            ]);

            foreach ($validated['variants'] as $variantData) {
                // Create ProductColor
                $productColor = $product->colors()->create([
                    'color_id' => $variantData['color_id'],
                    'box_number' => $variantData['box_number'] ?? null,
                    'stock' => 0, // Will be updated by sizes
                ]);

                foreach ($variantData['sizes'] as $sizeData) {
                    // Create ProductSize
                    $productColor->sizes()->create([
                        'size_id' => $sizeData['size_id'],
                        'stock' => $sizeData['stock'],
                        'box_number' => $sizeData['box_number'] ?? null,
                        // price can be overriden here if needed, but not in this basic form
                        // sku is auto-generated
                    ]);
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'colors.color', 'colors.sizes.size']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized action.');

        $product->load(['colors.sizes', 'subcategory']);
        $categories = \App\Models\Category::with('subcategories')->get();
        $colors = \App\Models\Color::all();
        $sizes = \App\Models\Size::all();

        return view('products.edit', compact('product', 'categories', 'colors', 'sizes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized action.');

        // Filter out incomplete variants before validation
        if ($request->has('variants')) {
            $filteredVariants = collect($request->variants)->filter(function ($variant) {
                // Keep if it has a color_id AND at least one size with a size_id and stock
                return !empty($variant['color_id']) && 
                       !empty($variant['sizes']) && 
                       collect($variant['sizes'])->filter(function ($size) {
                           return !empty($size['size_id']);
                       })->count() > 0;
            })->toArray();
            
            $request->merge(['variants' => $filteredVariants]);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:sub_categories,id',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'costing_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.color_id' => 'required|exists:colors,id',
            'variants.*.box_number' => 'nullable|string|max:255',
            'variants.*.sizes' => 'required|array|min:1',
            'variants.*.sizes.*.size_id' => 'required|exists:sizes,id',
            'variants.*.sizes.*.stock' => 'required|integer|min:0',
            'variants.*.sizes.*.sold' => 'nullable|integer|min:0',
            'variants.*.sizes.*.box_number' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $product) {
            $product->update([
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'name' => $validated['name'],
                'type' => $validated['type'] ?? null,
                'selling_price' => $validated['selling_price'],
                'costing_price' => $validated['costing_price'] ?? 0,
                'description' => $validated['description'],
            ]);

            $keepColorIds = [];
            
            foreach ($validated['variants'] as $variantData) {
                // Find or create the ProductColor for this product + color_id
                $productColor = \App\Models\ProductColor::updateOrCreate([
                    'product_id' => $product->id,
                    'color_id' => $variantData['color_id']
                ], [
                    'box_number' => $variantData['box_number'] ?? null
                ]);
                $keepColorIds[] = $productColor->id;

                $keepSizeIds = [];
                foreach ($variantData['sizes'] as $sizeData) {
                    // Update or create the ProductSize for this color + size_id
                    $productSize = \App\Models\ProductSize::updateOrCreate([
                        'product_color_id' => $productColor->id,
                        'size_id' => $sizeData['size_id']
                    ], [
                        'stock' => $sizeData['stock'],
                        'sold' => $sizeData['sold'] ?? 0,
                        'box_number' => $sizeData['box_number'] ?? null
                    ]);
                    $keepSizeIds[] = $productSize->id;
                }

                // Safely remove sizes not in the request
                $productColor->sizes()->whereNotIn('id', $keepSizeIds)->get()->each(function($size) {
                    try {
                        $size->delete();
                    } catch (\Exception $e) {
                        // Restricted (has sales)
                    }
                });

                $productColor->updateStock();
            }

            // Safely remove colors not in the request
            $product->colors()->whereNotIn('id', $keepColorIds)->get()->each(function($color) {
                try {
                    $color->delete();
                } catch (\Exception $e) {
                    // Restricted (has sizes with sales)
                }
            });
            
            $product->updateStock();
        });

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        abort(403, 'Deletion of products is disabled.');
        // $product->delete();
        // return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Display a listing of products with low stock.
     */
    public function lowStock()
    {
        $lowStockVariants = \App\Models\ProductSize::where('stock', '<', 2)
            ->with(['color.product.category', 'size'])
            ->get();

        return view('products.low_stock', compact('lowStockVariants'));
    }

    /**
     * Show the dedicated lookup page.
     */
    public function lookup()
    {
        return view('products.lookup');
    }

    /**
     * Search for products by SKU or Name.
     */
    public function search(Request $request)
    {
        $query = $request->input('search');

        if (empty($query)) {
            return redirect()->route('products.index');
        }

        // 1. Try exact Product SKU
        $product = Product::where('sku', $query)->with(['category', 'colors.color', 'colors.sizes.size'])->first();
        if ($product) {
            return view('products.lookup', compact('product', 'query'));
        }

        // 2. Try exact ProductSize SKU
        $productSize = \App\Models\ProductSize::where('sku', $query)->with(['color.product.category', 'color.product.colors.sizes', 'size'])->first();
        if ($productSize && $productSize->color && $productSize->color->product) {
            $product = $productSize->color->product;
            return view('products.lookup', compact('product', 'productSize', 'query'));
        }

        // 3. Search by name or partial SKU
        $products = Product::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->with(['category', 'subcategory'])
            ->paginate(12);

        if ($products->total() === 1) {
            return redirect()->route('products.show', $products->first());
        }

        return view('products.search_results', compact('products', 'query'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json'
        ]);

        $file = $request->file('file');
        $data = json_decode(file_get_contents($file->getRealPath()), true);

        if (!$data) {
            return back()->with('error', 'Invalid JSON file.');
        }

        $count = 0;
        DB::transaction(function () use ($data, &$count) {
            foreach ($data as $item) {
                // Ensure Attributes exist
                $category = Category::firstOrCreate(['name' => $item['Category']]);
                $subcategory = null;
                if (!empty($item['SubCategory'])) {
                    $subcategory = SubCategory::firstOrCreate([
                        'name' => $item['SubCategory'],
                        'category_id' => $category->id
                    ]);
                }

                $color = Color::firstOrCreate(['name' => $item['Color']]);
                $size = Size::firstOrCreate(['name' => $item['Size']]);

                // Create or Update Product - Override info if SKU matches
                $product = null;
                if (isset($item['id'])) {
                    $product = Product::find($item['id']);
                }
                
                if (!$product) {
                    $product = Product::firstOrCreate(
                        ['sku' => $item['Product SKU']],
                        [
                            'name' => $item['Product Name'],
                            'category_id' => $category->id,
                            'subcategory_id' => $subcategory ? $subcategory->id : null,
                            'type' => $item['Product Type'] ?? null,
                            'selling_price' => $item['Selling Price'] ?? 0,
                            'costing_price' => $item['Cost Price'] ?? 0,
                            'wirehouse_cost' => $item['Wirehouse Cost'] ?? 0,
                            'description' => $item['Description'] ?? '',
                            'stock' => 0,
                            'is_active' => true, // Ensure it's active on import/update
                        ]
                    );
                }
                // If product already existed (found by ID or SKU), we do NOT update it.

                // Ensure ProductColor exists
                $productColor = ProductColor::firstOrCreate(
                    ['product_id' => $product->id, 'color_id' => $color->id],
                    [
                        'box_number' => $item['Box Number'] ?? '',
                        'stock' => 0
                    ]
                );

                // Ensure ProductSize exists - use SKU as primary search key to avoid duplicates
                $psSearch = !empty($item['sku'])
                    ? ['sku' => $item['sku']]
                    : ['product_color_id' => $productColor->id, 'size_id' => $size->id];

                ProductSize::firstOrCreate(
                    $psSearch,
                    [
                        'product_color_id' => $productColor->id,
                        'size_id' => $size->id,
                        'stock' => 0,
                        'price' => $item['price'] ?? 0,
                    ]
                );
                
                $count++;
            }
        });

        return back()->with('success', "Products imported successfully ($count items processed).");
    }

}
