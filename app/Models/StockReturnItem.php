<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReturnItem extends Model
{
    protected $guarded = [];

    public function session()
    {
        return $this->belongsTo(StockReturnSession::class, 'session_id');
    }
}
