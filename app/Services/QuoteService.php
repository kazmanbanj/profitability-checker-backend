<?php

namespace App\Services;

use App\Exceptions\ApiNotFoundException;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteService
{
    public function list($request): array
    {
        $paginated = $request->boolean('paginated', true);
        $limit = $request->get('limit', 10);
        $query = Quote::with('lineItems')->latest('id');

        return $paginated
            ? $query->paginate($limit)->toArray()
            : $query->get()->toArray();
    }

    public function show(string $quoteId): array
    {
        $quote = Quote::find($quoteId);
        if (! $quote) {
            throw new ApiNotFoundException(__('Quote not found'));
        }

        return $quote->toArray();
    }

    public function analyze(array $request): array
    {
        return DB::transaction(function () use ($request) {
            $quote = Quote::create($request);

            if (! empty($request['line_items']) && is_array($request['line_items'])) {
                $quote->lineItems()->createMany($request['line_items']);
            }
            $suggestions = $quote->calculateProfitability();
            $quote->aiAnalysisVersions()->create([
                'suggestions' => $suggestions,
            ]);
            $quote->update([
                'ai_profitability_suggestions' => $suggestions,
            ]);

            return $quote->toArray();
        });
    }

    public function reAnalyze(Quote $quote, array $request): array
    {
        return DB::transaction(function () use ($quote, $request) {
            $currentSuggestions = $quote->ai_profitability_suggestions;

            if (is_string($currentSuggestions)) {
                $currentSuggestions = json_decode($currentSuggestions, true);
            }

            $suggestions = $quote->calculateProfitability(
                (array) $currentSuggestions,
                $request,
                true
            );

            $quote->aiAnalysisVersions()->create([
                'suggestions' => $suggestions,
            ]);
            $quote->update([
                'ai_profitability_suggestions' => $suggestions,
            ]);

            return $quote->toArray();
        });
    }

    public function versions(Quote $quote, Request $request): array
    {
        $paginated = $request->boolean('paginated', true);
        $limit = $request->get('limit', 10);
        $query = $quote->aiAnalysisVersions()->latest('id');

        return $paginated
            ? $query->paginate($limit)->toArray()
            : $query->get()->toArray();
    }
}
