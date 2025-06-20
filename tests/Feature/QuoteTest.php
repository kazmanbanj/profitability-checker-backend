<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_analyze_quote(): void
    {
        $payload = [
            'line_items' => [
                [
                    'name' => fake()->name(),
                    'cost_price' => rand(10, 100),
                    'sell_price' => rand(10, 100),
                    'quantity' => rand(10, 100),
                    'additional_info' => [
                        'MPN' => fake()->name(),
                        'SKU' => fake()->name(),
                    ],
                ],
            ],
            'labor_hours' => rand(10, 100),
            'labor_cost_per_hour' => rand(10, 100),
            'fixed_overheads' => rand(10, 100),
            'target_profit_margin' => rand(10, 50),
        ];

        $this->post('/api/v1/quotes/analyze', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'labor_hours',
                    'labor_cost_per_hour',
                    'fixed_overheads',
                    'target_profit_margin',
                    'updated_at',
                    'created_at',
                    'id',
                    'ai_profitability_suggestions' => [
                        'labor_hours',
                        'labor_cost_per_hour',
                        'fixed_overheads',
                        'target_profit_margin',
                        'total_cost',
                        'cost_of_goods_sold',
                        'total_revenue',
                        'labor_cost',
                        'gross_profit',
                        'profit_margin',
                        'meets_target',
                        'currency_symbol',
                        'line_items' => [
                            '*' => [
                                'id',
                                'name',
                                'sell_price',
                                'cost_price',
                                'quantity',
                                'additional_info' => [
                                    'MPN',
                                    'SKU',
                                ],
                                'margin_percent',
                            ],
                        ],
                        'labor_suggestions' => [],
                        'ai_suggestions' => [],
                    ],
                ],
                'message',
                'error',
            ]);
    }
}
