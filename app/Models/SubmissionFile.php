<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFile extends Model
{
    //
     protected $fillable = [
        'section_submission_id',
        'submission_requirement_id',
        'uploaded_by',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
        'review_status',
        'review_remarks',
    ];

    public function sectionSubmission(): BelongsTo
    {
        return $this->belongsTo(
            SectionSubmission::class,
            'section_submission_id'
        );
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(
            SubmissionRequirement::class,
            'submission_requirement_id'
        );
    }
}
