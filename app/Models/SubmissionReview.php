<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionReview extends Model
{
    protected $fillable = [
        'section_submission_id',
        'reviewed_by',
        'decision',
        'remarks',
        'file_feedback',
    ];

    protected $casts = [
        'file_feedback' => 'array',
    ];

    public function sectionSubmission(): BelongsTo
    {
        return $this->belongsTo(
            SectionSubmission::class
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }
}