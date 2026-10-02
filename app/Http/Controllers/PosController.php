<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::where('is_active', true)->with(['category', 'colors.color', 'colors.sizes.size'])->latest()->get();
        // Transform for JS usage if needed, or pass as is
        return view('pos.index', compact('products'));
    }

    public function exchange()
    {
        $products = \App\Models\Product::where('is_active', true)->with(['category', 'colors.color', 'colors.sizes.size'])->latest()->get();
        return view('pos.exchange', compact('products'));
    }
}
