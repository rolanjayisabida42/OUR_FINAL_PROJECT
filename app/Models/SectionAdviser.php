<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionAdviser extends Model
{
     // FILLABLE FIELDS
    protected $fillable = [
        'teacher_id',
        'section_id',
    ];

    // RELATIONSHIP: ADVISER BELONGS TO TEACHER
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // RELATIONSHIP: ADVISER BELONGS TO SECTION
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

}
