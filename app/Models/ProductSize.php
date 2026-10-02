<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    protected $fillable = ['product_color_id', 'size_id', 'stock', 'sold', 'box_number', 'price', 'sku', 'barcode_image'];

    protected static function booted()
    {
        static::saving(function ($item) {
            if (empty($item->sku) && $item->color && $item->color->product) {
                $item->sku = $item->generateSku();
            }
        });

        static::saved(function ($item) {
            $item->color->updateStock();
            $item->color->updateSold();
        });

        static::deleted(function ($item) {
            $item->color->updateStock();
            $item->color->updateSold();
        });
    }

    public function generateSku()
    {
        // format: product_sku + color_id + size_id
        return $this->color->product->sku . $this->color->color_id . $this->size_id;
    }

    public function color(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    public function size(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
}
