<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIntakeSession extends Model
{
    protected $fillable = ['reference', 'created_by'];

    public function items()
    {
        return $this->hasMany(StockIntakeItem::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
