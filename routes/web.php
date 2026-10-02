<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockIntakeController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Attributes Routes
    Route::get('/attributes', [AttributeController::class, 'index'])->name('attributes.index');
    Route::post('/attributes/category', [AttributeController::class, 'storeCategory'])->name('attributes.category.store');
    Route::delete('/attributes/category/{category}', [AttributeController::class, 'destroyCategory'])->name('attributes.category.destroy');
    Route::post('/attributes/subcategory', [AttributeController::class, 'storeSubCategory'])->name('attributes.subcategory.store');
    Route::delete('/attributes/subcategory/{subcategory}', [AttributeController::class, 'destroySubCategory'])->name('attributes.subcategory.destroy');
    Route::post('/attributes/color', [AttributeController::class, 'storeColor'])->name('attributes.color.store');
    Route::delete('/attributes/color/{color}', [AttributeController::class, 'destroyColor'])->name('attributes.color.destroy');
    Route::post('/attributes/size', [AttributeController::class, 'storeSize'])->name('attributes.size.store');
    Route::delete('/attributes/size/{size}', [AttributeController::class, 'destroySize'])->name('attributes.size.destroy');
    Route::post('/attributes/import', [AttributeController::class, 'import'])->name('attributes.import');

    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low_stock');
    Route::resource('products', ProductController::class);
    Route::get('/lookup', [ProductController::class, 'lookup'])->name('products.lookup');
    Route::get('/lookup/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class)->only(['index']);
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/exchange', [PosController::class, 'exchange'])->name('pos.exchange');
    Route::get('/orders/export-bulk-json', [OrderController::class, 'exportBulkJson'])->name('orders.export_bulk_json');
    Route::resource('orders', \App\Http\Controllers\OrderController::class)->only(['index', 'show', 'store']);
    Route::get('/orders/{order}/invoice', [\App\Http\Controllers\OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/export-json', [OrderController::class, 'exportJson'])->name('orders.export_json');
    Route::get('/orders-exchange-data', [OrderController::class, 'getExchangeData'])->name('orders.exchange.data');
    Route::post('/orders-exchange-process', [OrderController::class, 'processExchange'])->name('orders.exchange.process');
    Route::get('/exchanges', [\App\Http\Controllers\ProductExchangeController::class, 'index'])->name('exchanges.index');
    
    // Stock Intake
    Route::get('/stock-intake', [StockIntakeController::class, 'terminal'])->name('stock.intake.terminal');
    Route::get('/stock-intake/lookup', [StockIntakeController::class, 'lookup'])->name('stock.intake.lookup');
    Route::post('/stock-intake', [StockIntakeController::class, 'store'])->name('stock.intake.store');
    Route::get('/stock-history', [StockIntakeController::class, 'index'])->name('stock.intake.index');

    // Stock Return
    Route::get('/stock-return', [\App\Http\Controllers\StockReturnController::class, 'terminal'])->name('stock.return.terminal');
    Route::get('/stock-return/lookup', [\App\Http\Controllers\StockReturnController::class, 'lookup'])->name('stock.return.lookup');
    Route::post('/stock-return', [\App\Http\Controllers\StockReturnController::class, 'store'])->name('stock.return.store');
    Route::get('/stock-return-history', [\App\Http\Controllers\StockReturnController::class, 'index'])->name('stock.return.index');

    // Reports
    Route::get('/reports/sales', [\App\Http\Controllers\ReportController::class, 'sales'])->name('reports.sales');

    // Bulk / Single SMS (Admin + Moderator)
    Route::middleware('role:admin,moderator')->group(function () {
        Route::get('/bulk-sms', [SmsController::class, 'index'])->name('sms.index');
        Route::post('/bulk-sms/send', [SmsController::class, 'send'])->name('sms.send');
    });

    // User Management (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
