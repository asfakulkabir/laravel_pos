<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Sales Report API
    Route::get('/sells-report', [\App\Http\Controllers\ReportController::class, 'salesApi']);
});
