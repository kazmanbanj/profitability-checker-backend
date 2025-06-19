<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyzeQuoteRequest;
use App\Http\Requests\ReAnalyzeQuoteRequest;
use App\Models\Quote;
use App\Services\QuoteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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

    public function show(string $quoteId)
    {
        $quote = $this->quoteService->show($quoteId);

        return (new ApiResponse(
            data: $quote,
            message: __('Quote retrieved successfully')
        ))->asSuccessful();
    }

    public function analyze(AnalyzeQuoteRequest $request)
    {
        $response = $this->quoteService->analyze($request->validated());

        return (new ApiResponse(
            data: $response,
            message: __('Quote analyzed successfully')
        ))->asSuccessful();
    }

    public function reAnalyze(Quote $quote, ReAnalyzeQuoteRequest $request)
    {
        $response = $this->quoteService->reAnalyze($quote, $request->validated());

        return (new ApiResponse(
            data: $response,
            message: __('Quote re-analyzed successfully')
        ))->asSuccessful();
    }

    public function versions(Quote $quote, Request $request)
    {
        $response = $this->quoteService->versions($quote, $request);

        return (new ApiResponse(
            data: $response,
            message: __('Quote suggestion versions retrieved successfully')
        ))->asSuccessful();
    }

    public function exportAnalysis(Quote $quote)
    {
        $pdfTitle = 'Quote Analysis';
        $companyName = 'AV dealers'; // Assumed company name; should be user-defined.
        $analysis = $quote->ai_profitability_suggestions ?? $quote->calculateProfitability();
        $pdf = PDF::loadView('pdf-exports.quote-summary', compact('pdfTitle', 'companyName', 'analysis'));

        return $pdf->download('quote-analysis.pdf');
    }
}
