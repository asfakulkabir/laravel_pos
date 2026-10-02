<?php

namespace App\Http\Controllers;

use App\Models\ProductSize;
use App\Models\StockReturnSession;
use App\Models\StockReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockReturnController extends Controller
{
    /**
     * Display a listing of stock return history.
     */
    public function index(Request $request)
    {
        $sessions = StockReturnSession::with(['user'])
            ->latest()
            ->take(50)
            ->get();

        $selectedSession = null;
        $selectedItems = collect();
        $selectedSummary = null;

        $sessionId = $request->query('session_id');
        
        if ($sessionId) {
            $selectedSession = StockReturnSession::with(['user', 'items'])->find($sessionId);
        } elseif ($sessions->isNotEmpty()) {
            $selectedSession = StockReturnSession::with(['user', 'items'])->find($sessions->first()->id);
        }

        if ($selectedSession) {
            $selectedItems = $selectedSession->items;
            $selectedSummary = (object) [
                'id' => $selectedSession->id,
                'reference' => $selectedSession->reference,
                'created_at' => $selectedSession->created_at,
                'created_by' => $selectedSession->user,
                'item_count' => $selectedSession->items->count(),
                'total_quantity' => $selectedSession->items->sum('quantity'),
            ];
        }

        return view('stock_returns.index', [
            'sessions' => $sessions,
            'selected_session' => $selectedSession,
            'selected_items' => $selectedItems,
            'selected_summary' => $selectedSummary,
        ]);
    }

    /**
     * Show the return scanning terminal.
     */
    public function terminal()
    {
        return view('stock_returns.terminal');
    }

    /**
     * Lookup SKU details. Reuses similar logic but strictly for ID verification.
     */
    public function lookup(Request $request)
    {
        $sku = $request->query('sku');
        
        $productSize = ProductSize::with(['color.product', 'size'])
            ->where('sku', $sku)
            ->first();

        if (!$productSize) {
            return response()->json(['error' => 'Barcode not found.'], 404);
        }

        return response()->json([
            'size_id' => $productSize->id,
            'product' => $productSize->color->product->name,
            'color' => $productSize->color->color->name,
            'size' => $productSize->size->name,
            'sku' => $productSize->sku,
            'current_stock' => $productSize->stock,
        ]);
    }

    /**
     * Process stock return entries.
     */
    public function store(Request $request)
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.size_id' => 'required|exists:product_sizes,id',
            'entries.*.quantity' => 'required|integer|min:1',
            'entries.*.reason' => 'nullable|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // Create Session
                $session = StockReturnSession::create([
                    'reference' => 'RET-' . strtoupper(Str::random(8)),
                    'created_by' => auth()->id(),
                ]);

                $items = [];
                $totalQty = 0;

                foreach ($request->entries as $entry) {
                    $productSize = ProductSize::with(['color.product', 'size'])->lockForUpdate()->find($entry['size_id']);
                    
                    $prevStock = $productSize->stock;
                    
                    if ($request->boolean('update_stock')) {
                        if ($productSize->stock < $entry['quantity']) {
                            throw new \Exception("Insufficient stock for SKU [{$productSize->sku}]. Current: {$prevStock}, Return: {$entry['quantity']}");
                        }
                        $productSize->decrement('stock', $entry['quantity']);
                    }
                    
                    $newStock = $productSize->stock;

                    $item = StockReturnItem::create([
                        'session_id' => $session->id,
                        'sku' => $productSize->sku,
                        'product_name' => $productSize->color->product->name,
                        'color_name' => $productSize->color->color->name,
                        'size_name' => $productSize->size->name,
                        'quantity' => $entry['quantity'],
                        'reason' => $entry['reason'] ?? 'N/A',
                        'previous_stock' => $prevStock,
                        'new_stock' => $newStock,
                    ]);

                    $items[] = [
                        'size_id' => $productSize->id,
                        'product' => $item->product_name,
                        'color' => $item->color_name,
                        'size' => $item->size_name,
                        'sku' => $item->sku,
                        'reason' => $item->reason,
                        'previous_stock' => $item->previous_stock,
                        'new_stock' => $item->new_stock,
                        'returned' => $item->quantity,
                    ];
                    
                    $totalQty += $entry['quantity'];
                }

                return response()->json([
                    'session' => [
                        'reference' => $session->reference,
                        'created_at' => $session->created_at->toISOString(),
                        'total_items' => count($items),
                        'total_quantity' => $totalQty,
                    ],
                    'items' => $items,
                    'title' => 'Stock Return Summary',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process return: ' . $e->getMessage()], 500);
        }
    }
}
