<?php

namespace Tests\Unit;

use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitabilityCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected $quote;

    protected $lineItems;

    protected function setUp(): void
    {
        parent::setUp();
        [$this->quote, $this->lineItems] = $this->createQuoteWithLineItems();
    }

    protected function createQuoteWithLineItems(array $payload = [], array $lineItems = [])
    {
        $payload = array_merge([
            'labor_hours' => 4,
            'labor_cost_per_hour' => 34,
            'fixed_overheads' => 10,
            'target_profit_margin' => 12,
        ], $payload);

        $lineItems = $lineItems ?: [
            [
                'name' => fake()->name(),
                'cost_price' => (float) 2,
                'sell_price' => (float) 4,
                'quantity' => (int) 5,
                'additional_info' => [
                    'MPN' => fake()->name(),
                    'SKU' => fake()->name(),
                ],
            ],
            [
                'name' => fake()->name(),
                'cost_price' => (float) 3,
                'sell_price' => (float) 5,
                'quantity' => (int) 10,
                'additional_info' => [
                    'MPN' => fake()->name(),
                    'SKU' => fake()->name(),
                ],
            ],
        ];

        $quote = Quote::create($payload);
        $quote->lineItems()->createMany($lineItems);

        $suggestions = $quote->calculateProfitability();

        $quote->aiAnalysisVersions()->create(['suggestions' => $suggestions]);
        $quote->update(['ai_profitability_suggestions' => $suggestions]);

        return [$quote, $lineItems];
    }

    public function test_quote_total_revenue_is_calculated_correctly()
    {
        $expected = collect($this->lineItems)->sum(fn ($item) => $item['sell_price'] * $item['quantity']);
        $actual = $this->quote->calculateTotalRevenue($this->quote, $this->lineItems);
        $this->assertEquals($expected, $actual);
        $this->assertEquals(70, $actual);
    }

    public function test_quote_total_cost_is_calculated_correctly()
    {
        $expected = collect($this->lineItems)->sum(fn ($item) => $item['cost_price'] * $item['quantity']);
        $actual = $this->quote->calculateTotalCost($this->quote, $this->lineItems);
        $this->assertEquals($expected, $actual);
        $this->assertEquals(40, $actual);
    }

    public function test_quote_labor_cost_is_calculated_correctly()
    {
        $expected = $this->quote->labor_hours * $this->quote->labor_cost_per_hour;
        $actual = $this->quote->calculateLaborCost($this->quote, $this->lineItems);
        $this->assertEquals($expected, $actual);
        $this->assertEquals(136, $actual);
    }

    public function test_quote_profitability_is_calculated_correctly()
    {
        $totalCost = $this->quote->calculateTotalCost($this->quote, $this->lineItems);
        $laborCost = $this->quote->calculateLaborCost($this->quote, $this->lineItems);
        $totalRevenue = $this->quote->calculateTotalRevenue($this->quote, $this->lineItems);
        $costOfGoodsSold = $totalCost + $laborCost + $this->quote->fixed_overheads;
        $grossProfit = $totalRevenue - $costOfGoodsSold;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        $expected = [
            'labor_hours' => $this->quote->labor_hours,
            'labor_cost_per_hour' => $this->quote->labor_cost_per_hour,
            'fixed_overheads' => $this->quote->fixed_overheads,
            'target_profit_margin' => $this->quote->target_profit_margin,
            'total_cost' => $totalCost,
            'cost_of_goods_sold' => $costOfGoodsSold,
            'total_revenue' => $totalRevenue,
            'labor_cost' => $laborCost,
            'gross_profit' => $grossProfit,
            'profit_margin' => round($profitMargin, 2),
            'meets_target' => $profitMargin >= $this->quote->target_profit_margin,
        ];
        $actual = $this->quote->calculateProfitability($this->quote->toArray(), $this->lineItems);
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $actual);
            $this->assertEquals($value, $actual[$key]);
        }
    }
}
