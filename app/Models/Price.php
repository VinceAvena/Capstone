<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MODEL: the POSTED PRICE of one commodity at one stall on one day.
 *
 * price_date is kept as a plain "Y-m-d" string on purpose, so that
 * comparisons and the unique key behave the same on every database.
 */
class Price extends Model
{
    protected $fillable = [
        'stall_id', 'commodity_id', 'price', 'price_date',
        'posted_by', 'verified_by', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /** A price is "stale" when it was not updated today. */
    public function isStale(): bool
    {
        return $this->price_date < now()->toDateString();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Keeps only the CURRENT price of every stall + commodity pair, that is,
     * the row with the newest price_date. Used by the price board and by the
     * complaint form so shoppers always see the latest posted price.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        $latest = static::query()
            ->selectRaw('MAX(price_date) as latest_date, stall_id, commodity_id')
            ->where('price_date', '<=', now()->toDateString())
            ->groupBy('stall_id', 'commodity_id');

        return $query->select('prices.*')->joinSub($latest, 'latest', function ($join) {
            $join->on('prices.stall_id', '=', 'latest.stall_id')
                ->on('prices.commodity_id', '=', 'latest.commodity_id')
                ->on('prices.price_date', '=', 'latest.latest_date');
        });
    }
}
