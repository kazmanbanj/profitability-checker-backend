<?php

namespace App\Services;

use App\Models\Quote;

class QuoteService
{
    public function list($request): array
    {
        $paginated = $request->get('paginated', true);
        $limit = $request->get('limit', 10);
        $query = Quote::with('lineItems');

        return $paginated
            ? $query->paginate($limit)->toArray()
            : $query->get()->toArray();
    }

    public function analyze(array $request): array
    {
        $quote = Quote::create($request);
        foreach ($request['line_items'] as $item) {
            $quote->lineItems()->create($item);
        }
        $quoteProfitability = $quote->calculateProfitability();
        $aiGeneratedProfitabilitySuggestions = $quote->getAIGeneratedProfitabilitySuggestions($quoteProfitability);

        return array_merge($quoteProfitability, [
            'ai_suggestions' => $aiGeneratedProfitabilitySuggestions,
        ]);
    }
}
