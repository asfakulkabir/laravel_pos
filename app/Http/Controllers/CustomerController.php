<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index()
    {
        $customers = Customer::latest()->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function search(Request $request)
    {
        $query = $request->query('query');
        
        if (empty($query)) {
            return response()->json([]);
        }

        // Search by phone (partial match)
        $customers = Customer::where('phone', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'phone', 'address']);

        return response()->json($customers);
    }
}
