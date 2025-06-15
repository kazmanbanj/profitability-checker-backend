<?php

namespace App\Models;

use App\Services\AI\GeminiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->lineItems->sum(fn($item) => $item->sell_price * $item->quantity);
    }

    private function calculateTotalCost(): float
    {
        return $this->lineItems->sum(fn($item) => $item->cost_price * $item->quantity);
    }

    private function calculateLaborCost(): float
    {
        return $this->labor_hours * $this->labor_cost_per_hour;
    }

    /**
     * Calculate profitability metrics for the quote.
     *
     * @return array
     */
    public function calculateProfitability(): array
    {
        $totalRevenue = $this->calculateTotalRevenue();
        $totalCost = $this->calculateTotalCost();
        $laborCost = $this->calculateLaborCost();
        $costOfGoodsSold = $totalCost + $laborCost + $this->fixed_overheads;

        $grossProfit = $totalRevenue - $costOfGoodsSold;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        $health = $profitMargin >= $this->target_profit_margin ? 'green' : ($profitMargin >= 0.1 ? 'amber' : 'red');
        $lowMarginItems = [];

        foreach ($this->lineItems as $item) {
            $lowMarginItems[] = $item->getProfitabilityMetrics($this->target_profit_margin);
        }

        return [
            'health_status' => $health,
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
            'line_items' => $lowMarginItems,
            'currency_symbol' => '$',
        ];
    }

    public function getAIGeneratedProfitabilitySuggestions(array $quoteProfitability): string
    {
        $formattedData = json_encode($quoteProfitability, JSON_PRETTY_PRINT);
        $prompt = $this->buildPrompt($formattedData);

        $gemini = new GeminiService();
        $response = $gemini->generateContent($prompt);

        return $response ?? 'No suggestions available.';
    }

    private function buildPrompt(string $profitabilityData): string
    {
        return <<<EOT
            As a business analyst. Analyze the following quote data and provide actionable recommendations to improve profitability:

            $profitabilityData

            Please include the following:
            - Adjustments to meet target margins
            - Labor or resource allocation improvements
            - Suggested product swaps (if any)
            - A summary of the proposal's profitability health written in client-friendly language

            Respond clearly and concisely. Bullet points are welcome.
            EOT;
    }
}
