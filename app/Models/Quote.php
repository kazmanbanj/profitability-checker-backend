<?php

namespace App\Models;

use App\Services\AI\GeminiService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property float $labor_hours
 * @property float $labor_cost_per_hour
 * @property float $fixed_overheads
 * @property float $target_profit_margin
 * @property json $ai_profitability_suggestions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection $lineItems
 */
class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'labor_hours',
        'labor_cost_per_hour',
        'fixed_overheads',
        'target_profit_margin',
        'ai_profitability_suggestions',
    ];

    protected $casts = [
        'ai_profitability_suggestions' => 'json',
    ];

    /**
     * The lineItems attribute is hidden because detailed line item data is already included in the AI-generated suggestions.
     *
     * @var list<string>
     */
    protected $hidden = [
        'lineItems',
    ];

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function aiAnalysisVersions(): HasMany
    {
        return $this->hasMany(QuoteAiAnalysisVersion::class);
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

    public function calculateProfitability(array $previousSuggestion = [], array $userFeedback = [], bool $reassess = false): array
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

        $aiGenerated = $reassess
            ? extractJsonArray($this->getAIGeneratedProfitability($previousSuggestion, $userFeedback, $reassess))
            : extractJsonArray($this->getAIGeneratedProfitability($profitability));
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

    public function getAIGeneratedProfitability(array $quoteProfitability, array $userFeedback = [], bool $reassess = false): string
    {
        $prompt = $reassess
            ? $this->buildRePromptWithUserFeedback($quoteProfitability, $userFeedback)
            : $this->buildPrompt($quoteProfitability);
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
                "profitability_health_indicator": ("green" for good, "amber" for needs review, or "red" for poor)
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

    public function buildRePromptWithUserFeedback(array $previousSuggestion, array $userFeedback): string
    {
        $quoteJson = json_encode($previousSuggestion, JSON_PRETTY_PRINT);

        $lineItemInstructions = '';
        if (! empty($userFeedback['line_items'])) {
            $lineItemInstructions = "The user has made specific suggestions for individual quote items as follows:\n";
            foreach ($userFeedback['line_items'] as $item) {
                $itemSuggestion = $item['suggestion'] ?? null;
                $lineItemInstructions .= "- Line Item ID {$item['id']}: \"{$itemSuggestion}\"\n";
            }
        }

        $laborInstruction = '';
        if (! empty($userFeedback['labor_suggestions']['comment'])) {
            $laborInstruction = "Labor Feedback:\n- \"{$userFeedback['labor_suggestions']['comment']}\"\n";
        }

        $aiSuggestions = $userFeedback['ai_suggestions'] ?? [];
        $suggestionInstructions = '';
        foreach (['target_margin_adjustments', 'labor_allocation_improvements', 'product_swaps', 'profitability_summary'] as $key) {
            if (! empty($aiSuggestions[$key])) {
                $label = ucwords(str_replace('_', ' ', $key));
                $suggestionInstructions .= "- $label: \"{$aiSuggestions[$key]}\"\n";
            }
        }

        return <<<PROMPT
            You are a business analyst reviewing your previous AI-generated profitability suggestions based on the original quote data below.

            Original Quote Data:
            $quoteJson

            The user has submitted new feedback. Please update your original recommendations accordingly:

            $lineItemInstructions
            $laborInstruction
            Additional Suggestions:
            $suggestionInstructions

            Respond in the same structured JSON format used previously:
            {
                "suggestions": {
                    "target_margin_adjustments": (suggest new adjustment if there is a difference from previous suggestions),
                    "labor_allocation_improvements": (suggest new adjustment if there is a difference from previous suggestions),
                    "product_swaps": (suggest new adjustment if there is a difference from previous suggestions),
                    "profitability_summary": (suggest new adjustment if there is a difference from previous suggestions),
                    "profitability_health_indicator": ("green" for good, "amber" for needs review, or "red" for poor) – based on the updated profitability assessment
                },

                "items": [
                {
                    "name": (item name),
                    "status": ("Acceptable Margin" or "Low Margin"),
                    "suggestion": (suggest new adjustment if there is a difference from previous suggestions)
                },
                ...
                ],

                "labor": {
                    "estimated_sustainable_hours": (suggest new adjustment if there is a difference from previous suggestions),
                    "labor_hours_exceeded": true or false depending on if there is a difference from previous suggestions,
                    "comment": (suggest new comment if there is a difference from previous suggestions)
                }
            }
        PROMPT;
    }
}
