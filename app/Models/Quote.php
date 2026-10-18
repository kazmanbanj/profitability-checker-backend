<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property float $labor_hours
 * @property float $labor_cost_per_hour
 * @property float $fixed_overheads
 * @property float $target_profit_margin
 * @property json $ai_profitability_suggestions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection $lineItems
 */
class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'labor_hours',
        'labor_cost_per_hour',
        'fixed_overheads',
        'target_profit_margin',
        'ai_profitability_suggestions',
    ];

    protected $casts = [
        'ai_profitability_suggestions' => 'json',
    ];

    /**
     * The lineItems attribute is hidden because detailed line item data is already included in the AI-generated suggestions.
     *
     * @var list<string>
     */
    protected $hidden = [
        'lineItems',
    ];

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function aiAnalysisVersions(): HasMany
    {
        return $this->hasMany(QuoteAiAnalysisVersion::class);
    }

    public function calculateTotalRevenue(): float
    {
        return $this->lineItems->sum(fn ($item) => $item->sell_price * $item->quantity);
    }

    public function calculateTotalCost(): float
    {
        return $this->lineItems->sum(fn ($item) => $item->cost_price * $item->quantity);
    }

    public function calculateLaborCost(): float
    {
        return $this->labor_hours * $this->labor_cost_per_hour;
    }

}
