<?php

use App\Http\Controllers\Api\V1\QuoteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('quotes', QuoteController::class);
    Route::post('quotes/analyze', [QuoteController::class, 'analyze']);
    Route::post('quotes/{quote}/re-analyze', [QuoteController::class, 'reAnalyze']);
    Route::get('quotes/{quote}/versions', [QuoteController::class, 'versions']);
    Route::get('quotes/{quote}/export-analysis', [QuoteController::class, 'exportAnalysis']);
});
