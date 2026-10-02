<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = ['category_id', 'name', 'slug'];

    protected static function booted()
    {
        static::saving(function ($subcategory) {
            if (empty($subcategory->slug)) {
                $subcategory->slug = \Illuminate\Support\Str::slug($subcategory->category->name . '-' . $subcategory->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
