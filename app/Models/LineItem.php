<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $quote_id
 * @property string $name
 * @property float $cost_price
 * @property float $sell_price
 * @property int $quantity
 * @property array|null $additional_info
 * @property \Illuminate\Database\Eloquent\Collection $quote
 */
class LineItem extends Model
{
    protected $fillable = [
        'quote_id',
        'name',
        'cost_price',
        'sell_price',
        'quantity',
        'additional_info',
    ];

    protected $casts = [
        'additional_info' => 'json',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function getProfitabilityMetrics(?float $thresholdPercent = null): array
    {
        $cost = $this->cost_price * $this->quantity;
        $revenue = $this->sell_price * $this->quantity;

        $margin = $revenue > 0
            ? ($revenue - $cost) / $revenue
            : 0;

        $marginPercent = round($margin * 100, 2);
        $isLowMargin = $thresholdPercent ? $marginPercent < $thresholdPercent : null;

        return [
            'name' => $this->name,
            'sell_price' => $this->sell_price,
            'cost_price' => $this->cost_price,
            'quantity' => $this->quantity,
            'margin_percent' => $marginPercent,
            'is_low_margin' => $isLowMargin
        ];
    }
}
