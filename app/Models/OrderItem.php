<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 
        'product_size_id', 
        'product_name',
        'product_sku',
        'color_name',
        'size_name',
        'quantity', 
        'base_price',
        'unit_price', 
        'discount_type',
        'discount_value',
        'discount_amount',
        'subtotal'
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productSize(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductSize::class);
    }
}
