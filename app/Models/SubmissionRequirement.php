<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionRequirement extends Model
{
    protected $fillable = [
        'submission_setting_id',
        'name',
        'description',
        'position',
    ];

    public function submissionSetting(): BelongsTo
    {
        return $this->belongsTo(SubmissionSetting::class);
    }

    public function submissionFiles(): HasMany
    {
        return $this->hasMany(
            SubmissionFile::class,
            'submission_requirement_id'
        );
    }


}