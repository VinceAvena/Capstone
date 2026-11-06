<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MODEL: one entry in a complaint's history (a status change, a message
 * between the administration and the vendor, or a system notice).
 */
class ComplaintLog extends Model
{
    protected $fillable = [
        'complaint_id', 'user_id', 'type', 'audience',
        'from_status', 'to_status', 'message',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
