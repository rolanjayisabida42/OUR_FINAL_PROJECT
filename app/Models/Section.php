<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Section extends Model
{
     // FILLABLE FIELDS
    protected $fillable = [
        'school_year_id',
        'strand_id',
        'name',
        'grade_level',
        'is_active',
    ];

    // ATTRIBUTE CASTING
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // RELATIONSHIP: SECTION BELONGS TO SCHOOL YEAR
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    // RELATIONSHIP: SECTION BELONGS TO STRAND
    public function strand(): BelongsTo
    {
        return $this->belongsTo(Strand::class);
    }

    // RELATIONSHIP: SECTION HAS ONE ADVISER
    public function adviser(): HasOne
    {
        return $this->hasOne(SectionAdviser::class, 'section_id');
    }

    public function students(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Student::class,
            'section_student'
        )->withTimestamps();
    }
    
}
