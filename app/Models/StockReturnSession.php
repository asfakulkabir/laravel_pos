<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReturnSession extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(StockReturnItem::class, 'session_id');
    }
}
