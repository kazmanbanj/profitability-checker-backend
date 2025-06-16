<?php

namespace App\Models;

use App\Services\AI\GeminiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property float $labor_hours
 * @property float $labor_cost_per_hour
 * @property float $fixed_overheads
 * @property float $target_profit_margin
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property \Illuminate\Database\Eloquent\Collection $lineItems
 */
class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'labor_hours',
        'labor_cost_per_hour',
        'fixed_overheads',
        'target_profit_margin',
    ];

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    private function calculateTotalRevenue(): float
    {
        return $this->lineItems->sum(fn ($item) => $item->sell_price * $item->quantity);
    }

    private function calculateTotalCost(): float
    {
        return $this->lineItems->sum(fn ($item) => $item->cost_price * $item->quantity);
    }

    private function calculateLaborCost(): float
    {
        return $this->labor_hours * $this->labor_cost_per_hour;
    }

    public function calculateProfitability(): array
    {
        $totalRevenue = $this->calculateTotalRevenue();
        $totalCost = $this->calculateTotalCost();
        $laborCost = $this->calculateLaborCost();
        $costOfGoodsSold = $totalCost + $laborCost + $this->fixed_overheads;
        $grossProfit = $totalRevenue - $costOfGoodsSold;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        $lineItems = $this->lineItems->map(function ($item) {
            return $item->getProfitabilityMetrics();
        })->all();
        $profitability = [
            'labor_hours' => $this->labor_hours,
            'labor_cost_per_hour' => $this->labor_cost_per_hour,
            'fixed_overheads' => $this->fixed_overheads,
            'target_profit_margin' => $this->target_profit_margin,
            'total_cost' => $totalCost,
            'cost_of_goods_sold' => $costOfGoodsSold,
            'total_revenue' => $totalRevenue,
            'labor_cost' => $laborCost,
            'gross_profit' => $grossProfit,
            'profit_margin' => round($profitMargin, 2),
            'meets_target' => $profitMargin >= $this->target_profit_margin,
            'currency_symbol' => '$', // Assumed currency; should be user-defined.
            'line_items' => $lineItems,
        ];

        $aiGenerated = extractJsonArray($this->getAIGeneratedProfitability($profitability));
        $resultMap = collect($aiGenerated['items'] ?? [])->keyBy('name');
        $profitability['line_items'] = collect($profitability['line_items'])->map(function ($item) use ($resultMap) {
            $aiItem = $resultMap->get($item['name'], []);

            return array_merge($item, $aiItem);
        })->all();

        return array_merge($profitability, [
            'labor_suggestions' => $aiGenerated['labor'] ?? [],
            'ai_suggestions' => $aiGenerated['suggestions'] ?? [],
        ]);
    }

    public function getAIGeneratedProfitability(array $quoteProfitability): string
    {
        $prompt = $this->buildPrompt($quoteProfitability);
        $gemini = app(GeminiService::class);
        $response = $gemini->generateContent($prompt);

        return $response ?: 'No suggestions available.';
    }

    private function buildPrompt(array $profitabilityData): string
    {
        $profitabilityJson = json_encode($profitabilityData, JSON_PRETTY_PRINT);

        return <<<PROMPT
            You are a business analyst. Analyze the following quote data and provide recommendations to improve profitability:
            $profitabilityJson
            Respond with a JSON object containing three top-level keys: "suggestions", "items" and "labor".

            "suggestions": {
                "target_margin_adjustments": (suggested adjustments to meet target margins),
                "labor_allocation_improvements": (labor or resource allocation improvements),
                "product_swaps": (suggested product swaps, if any),
                "profitability_summary": (summary of the proposal's profitability health in client-friendly language),
                "profitability_health_indicator": ("green", "amber", or "red")
            }

            "items": [
            {
                "name": (item name),
                "status": ("Acceptable Margin" or "Low Margin"),
                "suggestion": (if margin is low)
            },
            ...
            ]

            Based on the provided estimated labor hours, assess whether the labor estimate is sustainable.
            "labor": {
                "estimated_sustainable_hours": your estimation
                "labor_hours_exceeded": true or false depending on whether the user's estimate exceeds your estimate by a large margin (e.g., > 20%)
                "comment": explanation of your judgment in client-friendly terms
            }

            Respond only with the JSON object.
            PROMPT;
    }
}
