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

    public function getProfitabilityMetrics(): array
    {
        $cost = (float) $this->cost_price * (int) $this->quantity;
        $revenue = (float) $this->sell_price * (int) $this->quantity;

        $marginPercent = 0.0;
        if ($revenue > 0) {
            $marginPercent = round((($revenue - $cost) / $revenue) * 100, 2);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sell_price' => (float) $this->sell_price,
            'cost_price' => (float) $this->cost_price,
            'quantity' => (int) $this->quantity,
            'additional_info' => $this->additional_info,
            'margin_percent' => $marginPercent,
        ];
    }
}
