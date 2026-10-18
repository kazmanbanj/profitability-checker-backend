<?php

use App\Http\Controllers\Api\V1\QuoteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('quotes', QuoteController::class)->only(['index', 'show']);

    Route::post('quotes', [QuoteController::class, 'analyze'])
        ->name('quotes.analyze')
        ->middleware('throttle:analyze');

    Route::post('quotes/{quote}/re-analyze', [QuoteController::class, 'reAnalyze'])
        ->name('quotes.re-analyze')
        ->middleware('throttle:re-analyze');

    Route::get('quotes/{quote}/versions', [QuoteController::class, 'versions'])->name('quotes.versions');
    Route::get('quotes/{quote}/export-analysis', [QuoteController::class, 'exportAnalysis'])->name('quotes.export-analysis');
});
