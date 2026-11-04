<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionSubmission extends Model
{
    //
    protected $fillable = [
        'section_id',
        'semester_id',
        'teacher_id',
        'status',
        'submitted_at',
        'reviewed_at',
        'principal_remarks',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

            /**
         * A classroom submission can have multiple
         * Principal review history records.
         */
        public function reviews()
        {
            return $this->hasMany(
                \App\Models\SubmissionReview::class,
                'section_submission_id'
            )->latest('id');
        }

}
