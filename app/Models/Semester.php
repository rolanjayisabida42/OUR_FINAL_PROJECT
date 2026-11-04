<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends Model
{
    //
     protected $fillable = [
        'school_year_id',
        'semester_number',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'semester_number' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
