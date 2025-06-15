<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Quote;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Services\QuoteService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuoteRequest;

class QuoteController extends Controller
{
    public function index(Request $request, QuoteService $quoteService)
    {
        $response = $quoteService->list($request);

        return (new ApiResponse(
            data: $response,
            message: __('Quotes retrieved successfully')
        ))->asSuccessful();
    }

    public function analyze(StoreQuoteRequest $request, QuoteService $quoteService)
    {
        $response = $quoteService->analyze($request->validated());

        return (new ApiResponse(
            data: $response,
            message: __('Quote created successfully')
        ))->asCreated();
    }
}
