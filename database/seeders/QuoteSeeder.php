<?php

namespace Database\Seeders;

use App\Models\Quote;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quotes = Quote::factory(4)->create();

        foreach ($quotes as $quote) {
            $lineItems = [
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
            ];

            $quote->lineItems()->createMany($lineItems);

            $suggestions = $quote->calculateProfitability();

            $quote->aiAnalysisVersions()->create([
                'suggestions' => $suggestions,
            ]);

            $quote->update([
                'ai_profitability_suggestions' => $suggestions,
            ]);
        }
    }
}
