<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'labor_hours' => rand(10, 100),
            'labor_cost_per_hour' => rand(10, 100),
            'fixed_overheads' => rand(10, 100),
            'target_profit_margin' => rand(10, 50),
        ];
    }
}
