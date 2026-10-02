<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductExchange extends Model
{
    protected $fillable = [
        'order_id',
        'returned_product_size_id',
        'new_product_size_id',
        'old_unit_price',
        'new_unit_price',
        'difference_amount',
        'reason',
        'price_note',
        'discount_amount',
        'discount_type',
        'user_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function returnedProductSize()
    {
        return $this->belongsTo(ProductSize::class, 'returned_product_size_id');
    }

    public function newProductSize()
    {
        return $this->belongsTo(ProductSize::class, 'new_product_size_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }}
