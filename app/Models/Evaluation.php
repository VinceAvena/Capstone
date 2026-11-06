<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MODEL: one respondent's ISO/IEC 25010 questionnaire.
 */
class Evaluation extends Model
{
    public const RESPONDENT_TYPES = [
        'it_expert' => 'IT Expert',
        'admin_personnel' => 'Market Administration Personnel',
        'inspector' => 'Market Inspector',
        'vendor' => 'Vendor',
        'shopper' => 'Shopper',
    ];

    protected $fillable = ['user_id', 'respondent_type', 'respondent_group', 'answers', 'comments'];

    protected function casts(): array
    {
        return ['answers' => 'array'];
    }

    /** IT experts form one group; everybody else is an "end user". */
    public static function groupFor(string $respondentType): string
    {
        return $respondentType === 'it_expert' ? 'it_expert' : 'end_user';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
