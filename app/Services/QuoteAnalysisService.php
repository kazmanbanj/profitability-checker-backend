<?php

namespace App\Services;

use App\Exceptions\AiServiceException;
use App\Helpers\JsonExtractor;
use App\Models\Quote;
use App\Services\AI\GroqService;

class QuoteAnalysisService
{
    public function __construct(protected GroqService $groq) {}

    /**
     * Calculate profitability metrics for a quote and enrich them with
     * AI-generated suggestions.
     *
     * When $reassess is true the AI is asked to revise its previous
     * suggestions ($previousSuggestion) in light of new $userFeedback,
     * so the raw profitability figures are not re-sent.
     */
    public function calculateProfitability(
        Quote $quote,
        array $previousSuggestion = [],
        array $userFeedback = [],
        bool $reassess = false
    ): array {
        $totalRevenue = $quote->calculateTotalRevenue();
        $totalCost    = $quote->calculateTotalCost();
        $laborCost    = $quote->calculateLaborCost();

        $costOfGoodsSold = $totalCost + $laborCost + $quote->fixed_overheads;
        $grossProfit     = $totalRevenue - $costOfGoodsSold;
        $profitMargin    = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        $lineItems = $quote->lineItems->map(fn ($item) => $item->getProfitabilityMetrics())->all();

        $profitability = [
            'labor_hours'           => $quote->labor_hours,
            'labor_cost_per_hour'   => $quote->labor_cost_per_hour,
            'fixed_overheads'       => $quote->fixed_overheads,
            'target_profit_margin'  => $quote->target_profit_margin,
            'total_cost'            => $totalCost,
            'cost_of_goods_sold'    => $costOfGoodsSold,
            'total_revenue'         => $totalRevenue,
            'labor_cost'            => $laborCost,
            'gross_profit'          => $grossProfit,
            'profit_margin'         => round($profitMargin, 2),
            'meets_target'          => $profitMargin >= $quote->target_profit_margin,
            'currency_symbol'       => config('business.currency_symbol'),
            'line_items'            => $lineItems,
        ];

        $aiRaw = $reassess
            ? $this->getAIGeneratedProfitability($previousSuggestion, $userFeedback, true)
            : $this->getAIGeneratedProfitability($profitability);

        $aiGenerated = JsonExtractor::extract($aiRaw);

        if (empty($aiGenerated)) {
            throw new AiServiceException('The AI service returned an unreadable response. Please try again later.');
        }

        $resultMap = collect($aiGenerated['items'] ?? [])->keyBy('name');

        $profitability['line_items'] = collect($profitability['line_items'])
            ->map(fn ($item) => array_merge($item, $resultMap->get($item['name'], [])))
            ->all();

        return array_merge($profitability, [
            'labor_suggestions' => $aiGenerated['labor']       ?? [],
            'ai_suggestions'    => $aiGenerated['suggestions'] ?? [],
        ]);
    }

    /**
     * Call the AI service and return the raw response string.
     */
    public function getAIGeneratedProfitability(
        array $quoteProfitability,
        array $userFeedback = [],
        bool $reassess = false
    ): string {
        $prompt = $reassess
            ? $this->buildRePromptWithUserFeedback($quoteProfitability, $userFeedback)
            : $this->buildPrompt($quoteProfitability);

        return $this->groq->generateContent($prompt) ?? throw new AiServiceException();
    }

    /**
     * Build the initial analysis prompt.
     */
    public function buildPrompt(array $profitabilityData): string
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

    /**
     * Build the re-analysis prompt that incorporates user feedback on
     * a previous set of suggestions.
     */
    public function buildRePromptWithUserFeedback(array $previousSuggestion, array $userFeedback): string
    {
        $quoteJson = json_encode($previousSuggestion, JSON_PRETTY_PRINT);

        $lineItemInstructions = '';
        if (! empty($userFeedback['line_items'])) {
            $lineItemInstructions = "The user has made specific suggestions for individual quote items as follows:\n";
            foreach ($userFeedback['line_items'] as $item) {
                $itemSuggestion        = $item['suggestion'] ?? null;
                $lineItemInstructions .= "- Line Item ID {$item['id']}: \"{$itemSuggestion}\"\n";
            }
        }

        $laborInstruction = '';
        if (! empty($userFeedback['labor_suggestions']['comment'])) {
            $laborInstruction = "Labor Feedback:\n- \"{$userFeedback['labor_suggestions']['comment']}\"\n";
        }

        $aiSuggestions        = $userFeedback['ai_suggestions'] ?? [];
        $suggestionInstructions = '';
        foreach (['target_margin_adjustments', 'labor_allocation_improvements', 'product_swaps', 'profitability_summary'] as $key) {
            if (! empty($aiSuggestions[$key])) {
                $label                   = ucwords(str_replace('_', ' ', $key));
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
