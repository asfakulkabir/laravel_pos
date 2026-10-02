<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductExchangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exchanges = \App\Models\ProductExchange::with([
            'order.items', 
            'returnedProductSize.color.product', 
            'returnedProductSize.size',
            'newProductSize.color.product', 
            'newProductSize.size',
            'user'
        ])->latest()->paginate(20);

        return view('exchanges.index', compact('exchanges'));
    }
}
