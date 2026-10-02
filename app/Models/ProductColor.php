<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    protected $fillable = ['product_id', 'color_id', 'color_image', 'stock', 'sold', 'box_number'];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function sizes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function getTotalStockAttribute()
    {
        return $this->relationLoaded('sizes') ? $this->sizes->sum('stock') : $this->sizes()->sum('stock');
    }

    public function updateStock()
    {
        $total = $this->sizes()->sum('stock');
        if ($this->stock != $total) {
            $this->stock = $total;
            $this->saveQuietly();
            $this->product->updateStock();
        }
    }

    public function updateSold()
    {
        $total = $this->sizes()->sum('sold');
        if ($this->sold != $total) {
            $this->sold = $total;
            $this->saveQuietly();
            $this->product->updateSold();
        }
    }
}
