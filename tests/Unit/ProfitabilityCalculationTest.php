<?php

namespace Tests\Unit;

use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitabilityCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected Quote $quote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->quote = $this->createQuoteWithLineItems();
    }

    /**
     * Create a Quote with two known line items and eager-load the relation
     * so the model's calculation methods can walk $this->lineItems.
     * No AI call is made here — that belongs in a feature/integration test.
     */
    protected function createQuoteWithLineItems(array $quoteOverrides = [], array $lineItems = []): Quote
    {
        $quote = Quote::create(array_merge([
            'labor_hours'          => 4,
            'labor_cost_per_hour'  => 34,
            'fixed_overheads'      => 10,
            'target_profit_margin' => 12,
        ], $quoteOverrides));

        $quote->lineItems()->createMany($lineItems ?: [
            [
                'name'            => 'Widget A',
                'cost_price'      => 2.0,
                'sell_price'      => 4.0,
                'quantity'        => 5,
                'additional_info' => null,
            ],
            [
                'name'            => 'Widget B',
                'cost_price'      => 3.0,
                'sell_price'      => 5.0,
                'quantity'        => 10,
                'additional_info' => null,
            ],
        ]);

        // Reload the relation so the in-memory model reflects the persisted items.
        $quote->load('lineItems');

        return $quote;
    }

    public function test_quote_total_revenue_is_calculated_correctly(): void
    {
        // (4 × 5) + (5 × 10) = 20 + 50 = 70
        $this->assertEquals(70.0, $this->quote->calculateTotalRevenue());
    }

    public function test_quote_total_cost_is_calculated_correctly(): void
    {
        // (2 × 5) + (3 × 10) = 10 + 30 = 40
        $this->assertEquals(40.0, $this->quote->calculateTotalCost());
    }

    public function test_quote_labor_cost_is_calculated_correctly(): void
    {
        // 4 hours × $34/hr = 136
        $this->assertEquals(136.0, $this->quote->calculateLaborCost());
    }

    public function test_quote_derived_profitability_figures_are_correct(): void
    {
        $totalRevenue    = $this->quote->calculateTotalRevenue();   // 70
        $totalCost       = $this->quote->calculateTotalCost();      // 40
        $laborCost       = $this->quote->calculateLaborCost();      // 136
        $costOfGoodsSold = $totalCost + $laborCost + $this->quote->fixed_overheads; // 40 + 136 + 10 = 186
        $grossProfit     = $totalRevenue - $costOfGoodsSold;        // 70 - 186 = -116
        $profitMargin    = round(($grossProfit / $totalRevenue) * 100, 2); // -165.71

        $this->assertEquals(70.0,    $totalRevenue);
        $this->assertEquals(40.0,    $totalCost);
        $this->assertEquals(136.0,   $laborCost);
        $this->assertEquals(186.0,   $costOfGoodsSold);
        $this->assertEquals(-116.0,  $grossProfit);
        $this->assertEquals(-165.71, $profitMargin);
        $this->assertFalse($profitMargin >= $this->quote->target_profit_margin);
    }
}
