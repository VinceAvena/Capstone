<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * MODEL: an overpricing complaint with photo evidence. This class also holds
 * the rules for COMPLAINT VERIFICATION and RESOLUTION TRACKING.
 *
 * Status flow (the only moves the system allows):
 *
 *   pending --> under_review --> confirmed --> resolved
 *      |              |
 *      +--> dismissed <+
 */
class Complaint extends Model
{
    public const STATUSES = [
        'pending' => 'Pending',
        'under_review' => 'Under review',
        'confirmed' => 'Confirmed overpricing',
        'dismissed' => 'Dismissed',
        'resolved' => 'Resolved',
    ];

    public const TRANSITIONS = [
        'pending' => ['under_review', 'dismissed'],
        'under_review' => ['confirmed', 'dismissed'],
        'confirmed' => ['resolved'],
        'dismissed' => [],
        'resolved' => [],
    ];

    protected $fillable = [
        'reference_no', 'shopper_id', 'stall_id', 'commodity_id', 'price_id',
        'posted_price', 'posted_price_date', 'reported_price', 'description',
        'photo_path', 'status', 'admin_remarks', 'resolution_notes',
        'handled_by', 'confirmed_at', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'posted_price' => 'decimal:2',
            'reported_price' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    /** Give every new complaint a readable reference number such as TP-20261102-A9K2F. */
    protected static function booted(): void
    {
        static::creating(function (Complaint $complaint) {
            if (! $complaint->reference_no) {
                $complaint->reference_no = 'TP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    // ---- Relationships ------------------------------------------------

    public function shopper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shopper_id');
    }

    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ComplaintLog::class)->orderBy('created_at')->orderBy('id');
    }

    // ---- Price comparison ---------------------------------------------

    /** How much more than the posted price the shopper says was charged. */
    public function overcharge(): float
    {
        return round((float) $this->reported_price - (float) $this->posted_price, 2);
    }

    public function overchargePercent(): float
    {
        $posted = (float) $this->posted_price;

        return $posted > 0 ? round($this->overcharge() / $posted * 100, 1) : 0.0;
    }

    // ---- Workflow -----------------------------------------------------

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function allowedTransitions(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    /** The vendor only learns about a complaint after it has been verified. */
    public function isVisibleToVendor(): bool
    {
        return in_array($this->status, ['confirmed', 'resolved'], true);
    }

    /** Messages between the administration and the vendor are allowed on verified complaints. */
    public function canExchangeMessages(): bool
    {
        return $this->isVisibleToVendor();
    }

    /**
     * Move the complaint to a new status, record it in the history, and
     * apply the repeat-violation rule when a complaint is confirmed.
     * Returns false when the move is not allowed by the status flow.
     */
    public function transitionTo(string $to, User $by, ?string $note = null): bool
    {
        if (! in_array($to, $this->allowedTransitions(), true)) {
            return false;
        }

        DB::transaction(function () use ($to, $by, $note) {
            $from = $this->status;

            $this->status = $to;
            $this->handled_by = $by->id;

            if ($to === 'confirmed') {
                $this->confirmed_at = now();
            }
            if ($to === 'resolved') {
                $this->resolved_at = now();
                $this->resolution_notes = $note;
            } elseif ($note) {
                $this->admin_remarks = $note;
            }

            $this->save();

            $this->logs()->create([
                'user_id' => $by->id,
                'type' => 'status',
                'audience' => 'all',
                'from_status' => $from,
                'to_status' => $to,
                'message' => $note,
            ]);

            // Repeat violation flagging happens the moment a complaint is confirmed.
            if ($to === 'confirmed' && $this->stall->refreshFlag()) {
                $this->logs()->create([
                    'user_id' => null,
                    'type' => 'system',
                    'audience' => 'vendor',
                    'message' => 'This stall has reached ' . config('tindapresyo.violation_threshold')
                        . ' confirmed overpricing complaints and is now flagged as a repeat violator.',
                ]);
            }
        });

        return true;
    }
}
