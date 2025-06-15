<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\QuoteController;
use App\Http\Controllers\Api\V1\AnalysisController;

Route::prefix('v1')->group(function () {
    Route::apiResource('quotes', QuoteController::class);
    Route::post('quotes/analyze', [QuoteController::class, 'analyze']);
    Route::get('quotes/{quote}/export-analysis', [QuoteController::class, 'exportAnalysis']);
});