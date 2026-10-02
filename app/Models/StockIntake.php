<?php

namespace App\Models;

use App\Models\ProductSize;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StockIntake extends Model
{
    protected $fillable = ['product_size_id', 'user_id', 'quantity', 'prev_stock', 'new_stock'];

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
