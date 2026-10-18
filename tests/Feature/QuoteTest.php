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
                    'name'            => 'HDMI Cable 2m',
                    'cost_price'      => 15.00,
                    'sell_price'      => 30.00,
                    'quantity'        => 5,
                    'additional_info' => [
                        'MPN' => 'HDM-2M-001',
                        'SKU' => 'SKU-00123',
                    ],
                ],
            ],
            'labor_hours'          => 8,
            'labor_cost_per_hour'  => 25.00,
            'fixed_overheads'      => 50.00,
            'target_profit_margin' => 20,
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
