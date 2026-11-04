<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionSetting extends Model
{
    protected $fillable = [
        'semester_id',
        'grade_scope',
        'opens_at',
        'deadline_at',
        'status',
        'instructions',
        'configured_by',
    ];

    protected $casts = [
        'opens_at' => 'datetime',
        'deadline_at' => 'datetime',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(SubmissionRequirement::class)
            ->orderBy('position');
    }

    public function isOpen(): bool
    {
        return $this->status === 'active'
            && $this->opens_at !== null
            && $this->deadline_at !== null
            && now()->between(
                $this->opens_at,
                $this->deadline_at
            );
    }

    public function reviews()
    {
        return $this->hasMany(
            \App\Models\SubmissionReview::class
        )->latest('id');
    }
    
}