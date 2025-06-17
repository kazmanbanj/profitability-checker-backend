<?php

namespace App\Services;

use App\Models\Quote;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($request) {
            $quote = Quote::create($request);

            if (! empty($request['line_items']) && is_array($request['line_items'])) {
                $quote->lineItems()->createMany($request['line_items']);
            }

            $quote->update([
                'ai_profitability_suggestions' => $quote->calculateProfitability(),
            ]);

            return $quote->toArray();
        });
    }
}
