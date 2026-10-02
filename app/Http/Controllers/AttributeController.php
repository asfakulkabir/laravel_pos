<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $categories = Category::with('subcategories')->latest()->get();
        $colors = Color::latest()->get();
        $sizes = Size::latest()->get();

        return view('attributes.index', compact('categories', 'colors', 'sizes'));
    }

    // --- Category Actions ---
    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categories']);
        Category::create(['name' => $request->name]);
        return back()->with('success', 'Category added successfully.');
    }

    public function destroyCategory(Category $category)
    {
        try {
            $category->delete();
            return back()->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete category as it contains subcategories or products.');
        }
    }

    // --- SubCategory Actions ---
    public function storeSubCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255'
        ]);
        SubCategory::create($request->all());
        return back()->with('success', 'SubCategory added successfully.');
    }

    public function destroySubCategory(SubCategory $subcategory)
    {
        try {
            $subcategory->delete();
            return back()->with('success', 'SubCategory deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete subcategory as it contains products.');
        }
    }

    // --- Color Actions ---
    public function storeColor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors',
             // 'code' => 'required|string|max:7' // If we add hex code later
        ]);
        Color::create($request->all());
        return back()->with('success', 'Color added successfully.');
    }

    public function destroyColor(Color $color)
    {
        try {
            $color->delete();
            return back()->with('success', 'Color deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete color as it is referenced in products.');
        }
    }

    // --- Size Actions ---
    public function storeSize(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50|unique:sizes']);
        Size::create($request->all());
        return back()->with('success', 'Size added successfully.');
    }

    public function destroySize(Size $size)
    {
        try {
            $size->delete();
            return back()->with('success', 'Size deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete size identifier as it is referenced in products.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:category,subcategory,color,size',
            'file' => 'required|file|mimes:json'
        ]);

        $file = $request->file('file');
        $data = json_decode(file_get_contents($file->getRealPath()), true);

        if (!$data) {
            return back()->with('error', 'Invalid JSON file.');
        }

        $count = 0;
        foreach ($data as $item) {
            switch ($request->type) {
                case 'category':
                    $exists = false;
                    if (isset($item['id']) && Category::find($item['id'])) {
                        $exists = true;
                    }
                    if (!$exists) {
                        Category::firstOrCreate(
                            ['name' => $item['name']],
                            ['slug' => $item['slug'] ?? \Illuminate\Support\Str::slug($item['name'])]
                        );
                    }
                    $count++;
                    break;

                case 'subcategory':
                    $exists = false;
                    if (isset($item['id']) && SubCategory::find($item['id'])) {
                        $exists = true;
                    }

                    if (!$exists && isset($item['category__name'])) {
                        $category = Category::firstOrCreate(['name' => $item['category__name']]);
                        SubCategory::firstOrCreate(
                            ['name' => $item['name'], 'category_id' => $category->id],
                            ['slug' => $item['slug'] ?? \Illuminate\Support\Str::slug($category->name . '-' . $item['name'])]
                        );
                        $count++;
                    }
                    break;

                case 'color':
                    $exists = false;
                    if (isset($item['id']) && Color::find($item['id'])) {
                        $exists = true;
                    }
                    if (!$exists) {
                        Color::firstOrCreate(['name' => $item['name']]);
                    }
                    $count++;
                    break;

                case 'size':
                    $exists = false;
                    if (isset($item['id']) && Size::find($item['id'])) {
                        $exists = true;
                    }
                    if (!$exists) {
                        Size::firstOrCreate(['name' => $item['name']]);
                    }
                    $count++;
                    break;
            }
        }

        return back()->with('success', ucfirst($request->type) . " imported successfully ($count items).");
    }
}
