<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIntakeItem extends Model
{
    protected $fillable = [
        'session_id', 
        'sku', 
        'product_name', 
        'color_name', 
        'size_name', 
        'quantity', 
        'previous_stock', 
        'new_stock'
    ];

    public function session()
    {
        return $this->belongsTo(StockIntakeSession::class, 'session_id');
    }
}
