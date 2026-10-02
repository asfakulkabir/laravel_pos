<?php

namespace App\Http\Controllers;

use App\Models\ProductSize;
use App\Models\StockIntake;
use App\Models\StockIntakeSession;
use App\Models\StockIntakeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockIntakeController extends Controller
{
    /**
     * Display a listing of stock intake history.
     */
    public function index(Request $request)
    {
        $sessions = StockIntakeSession::with(['user'])
            ->latest()
            ->take(50) // Show last 50 sessions in the sidebar
            ->get();

        $selectedSession = null;
        $selectedItems = collect();
        $selectedSummary = null;

        $sessionId = $request->query('session_id');
        
        if ($sessionId) {
            $selectedSession = StockIntakeSession::with(['user', 'items'])->find($sessionId);
        } elseif ($sessions->isNotEmpty()) {
            $selectedSession = StockIntakeSession::with(['user', 'items'])->find($sessions->first()->id);
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

        return view('stock_intakes.index', [
            'sessions' => $sessions,
            'selected_session' => $selectedSession,
            'selected_items' => $selectedItems,
            'selected_summary' => $selectedSummary,
        ]);
    }

    /**
     * Show the intake scanning terminal.
     */
    public function terminal()
    {
        return view('stock_intakes.terminal');
    }

    /**
     * Lookup SKU details for the scanning queue.
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
     * Process bulk stock intake entries.
     */
    public function store(Request $request)
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.size_id' => 'required|exists:product_sizes,id',
            'entries.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // Create Session
                $session = StockIntakeSession::create([
                    'reference' => 'INT-' . strtoupper(Str::random(8)),
                    'created_by' => auth()->id(),
                ]);

                $items = [];
                $totalQty = 0;

                foreach ($request->entries as $entry) {
                    $productSize = ProductSize::with(['color.product', 'size'])->lockForUpdate()->find($entry['size_id']);
                    
                    $prevStock = $productSize->stock;

                    if ($request->boolean('update_stock')) {
                        $productSize->increment('stock', $entry['quantity']);
                    }
                    
                    $newStock = $productSize->stock;

                    $item = StockIntakeItem::create([
                        'session_id' => $session->id,
                        'sku' => $productSize->sku,
                        'product_name' => $productSize->color->product->name,
                        'color_name' => $productSize->color->color->name,
                        'size_name' => $productSize->size->name,
                        'quantity' => $entry['quantity'],
                        'previous_stock' => $prevStock,
                        'new_stock' => $newStock,
                    ]);

                    $items[] = [
                        'size_id' => $productSize->id,
                        'product' => $item->product_name,
                        'color' => $item->color_name,
                        'size' => $item->size_name,
                        'sku' => $item->sku,
                        'previous_stock' => $item->previous_stock,
                        'new_stock' => $item->new_stock,
                        'added' => $item->quantity,
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
                    'title' => 'Stock Intake Summary',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process stock intake: ' . $e->getMessage()], 500);
        }
    }
}
