<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'subcategory_id', 'name', 'slug', 'sku', 'description', 
        'type', 'main_image', 'is_active', 'wirehouse_cost', 'selling_price', 'costing_price', 
        'stock', 'sold', 'barcode_image', 'qr_code'
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = static::generateNextSku();
            }
        });

        static::saving(function ($product) {
            // Slug Generation
            if ($product->isDirty('name') || empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product);
            }

            // Costing Price Calculation
            if ($product->selling_price !== null && (empty($product->costing_price) || $product->costing_price == 0)) {
                $product->costing_price = $product->selling_price * 0.6;
            }
        });
    }

    public static function generateNextSku()
    {
        // Simple numeric increment logic
        $lastSku = static::whereRaw('sku REGEXP "^[0-9]+$"')->orderByRaw('CAST(sku AS UNSIGNED) DESC')->value('sku');
        $next = $lastSku ? (int)$lastSku + 1 : 1;
        return str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }

    public static function generateUniqueSlug($product)
    {
        $baseSlug = \Illuminate\Support\Str::slug($product->name . '-' . $product->sku);
        $slug = $baseSlug;
        $counter = 1;
        while (static::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    public function getTotalStockAttribute()
    {
        // If relationship is loaded, use it to avoid extra queries
        if ($this->relationLoaded('colors')) {
            return $this->colors->sum(function($color) {
                return $color->relationLoaded('sizes') ? $color->sizes->sum('stock') : $color->sizes()->sum('stock');
            });
        }
        return $this->colors()->with('sizes')->get()->flatMap->sizes->sum('stock');
    }

    public function updateStock()
    {
        $total = $this->colors()->sum('stock');
        if ($this->stock != $total) {
            $this->stock = $total;
            $this->saveQuietly(); // Avoid triggering saving hooks again if not needed
        }
    }

    public function updateSold()
    {
        $total = $this->colors()->sum('sold');
        if ($this->sold != $total) {
            $this->sold = $total;
            $this->saveQuietly();
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }
}
