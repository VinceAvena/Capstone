<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MODEL: a market stall. Besides holding data, it applies the system rule
 * for REPEAT VIOLATION FLAGGING (see refreshFlag()).
 */
class Stall extends Model
{
    public const STATUSES = [
        'pending' => 'Pending approval',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    protected $fillable = [
        'user_id', 'stall_number', 'name', 'section', 'description',
        'status', 'is_flagged', 'flagged_at', 'flag_cleared_at',
    ];

    protected function casts(): array
    {
        return [
            'is_flagged' => 'boolean',
            'flagged_at' => 'datetime',
            'flag_cleared_at' => 'datetime',
        ];
    }

    // ---- Relationships ------------------------------------------------

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    // ---- Query scope --------------------------------------------------

    /** Only stalls that the administration has approved. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // ---- Business rules -----------------------------------------------

    /** The most recent posted price of one commodity at this stall (up to today). */
    public function latestPriceFor(int $commodityId): ?Price
    {
        return $this->prices()
            ->where('commodity_id', $commodityId)
            ->where('price_date', '<=', now()->toDateString())
            ->orderByDesc('price_date')
            ->first();
    }

    /** Has this stall posted at least one price today? */
    public function hasPostedToday(): bool
    {
        return $this->prices()->where('price_date', now()->toDateString())->exists();
    }

    /**
     * TRUSTED STALL badge (an advantage for honest vendors AND for shoppers).
     * A stall earns it when it is approved, not flagged, has posted prices
     * today, and every price posted today has been verified by an inspector.
     */
    public function isTrusted(): bool
    {
        if ($this->status !== 'active' || $this->is_flagged || ! $this->hasPostedToday()) {
            return false;
        }

        return ! $this->prices()
            ->where('price_date', now()->toDateString())
            ->whereNull('verified_at')
            ->exists();
    }

    /** On how many of the last N days (including today) did the stall post prices? */
    public function consistency(int $days = 7): int
    {
        $from = now()->subDays($days - 1)->toDateString();

        return (int) $this->prices()
            ->whereBetween('price_date', [$from, now()->toDateString()])
            ->distinct()
            ->count('price_date');
    }

    /**
     * Confirmed overpricing complaints counted toward the flag. After an
     * administrator clears a flag, only newer confirmations are counted.
     */
    public function violationCount(): int
    {
        return $this->complaints()
            ->whereNotNull('confirmed_at')
            ->when($this->flag_cleared_at, fn ($q, $clearedAt) => $q->where('confirmed_at', '>', $clearedAt))
            ->count();
    }

    /**
     * REPEAT VIOLATION FLAGGING. Marks the stall once its confirmed
     * complaints reach the limit set in config/tindapresyo.php.
     * Returns true only when the stall has just been flagged.
     */
    public function refreshFlag(): bool
    {
        if ($this->is_flagged) {
            return false;
        }

        if ($this->violationCount() >= config('tindapresyo.violation_threshold')) {
            $this->forceFill(['is_flagged' => true, 'flagged_at' => now()])->save();

            return true;
        }

        return false;
    }

    /** Administrator removes the flag; counting starts again from now. */
    public function clearFlag(): void
    {
        $this->forceFill([
            'is_flagged' => false,
            'flagged_at' => null,
            'flag_cleared_at' => now(),
        ])->save();
    }

    /** Vendors may post for their own approved stalls; staff for any approved stall. */
    public function canBePostedBy(?User $user): bool
    {
        if (! $user || $this->status !== 'active') {
            return false;
        }

        return $user->isStaff() || $user->id === $this->user_id;
    }
}
