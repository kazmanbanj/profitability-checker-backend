<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\AnalysisController;

Route::prefix('v1')->group(function () {
    Route::apiResource('quotes', QuoteController::class);
    Route::post('quotes/{quote}/analyze', [AnalysisController::class, 'analyze']);
    Route::post('quotes/{quote}/ai-suggestions', [AnalysisController::class, 'generateSuggestions']);
});