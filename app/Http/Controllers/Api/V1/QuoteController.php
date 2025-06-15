<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Services\QuoteService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuoteRequest;

class QuoteController extends Controller
{
    public function __construct(protected QuoteService $quoteService) {}

    public function index(Request $request)
    {
        $response = $this->quoteService->list($request);

        return (new ApiResponse(
            data: $response,
            message: __('Quotes retrieved successfully')
        ))->asSuccessful();
    }

    public function show(Quote $quote)
    {
        $quote = $quote->load('lineItems')->toArray();

        return (new ApiResponse(
            data: $quote,
            message: __('Quote retrieved successfully')
        ))->asSuccessful();
    }

    public function analyze(StoreQuoteRequest $request)
    {
        $response = $this->quoteService->analyze($request->validated());

        return (new ApiResponse(
            data: $response,
            message: __('Quote analyzed successfully')
        ))->asSuccessful();
    }

    public function exportAnalysis(Quote $quote)
    {
        $pdfTitle = 'Quote Analysis';
        $companyName = 'AV dealers';
        $analysis = $quote->calculateProfitability();
        $pdf = PDF::loadView('pdf-exports.quote-summary', compact('pdfTitle', 'companyName', 'analysis'));

        return $pdf->download('quote-analysis.pdf');
    }
}
