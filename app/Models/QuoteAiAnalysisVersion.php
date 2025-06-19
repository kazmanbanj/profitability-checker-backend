<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $quote_id
 * @property json $suggestions
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class QuoteAiAnalysisVersion extends Model
{
    protected $fillable = [
        'quote_id',
        'suggestions',
    ];

    protected $casts = [
        'suggestions' => 'json',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
