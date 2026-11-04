<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    //
      protected $fillable = [
        'lrn',
        'last_name',
        'first_name',
        'middle_name',
        'sex',
        'birth_date',
        'birth_place_province',
        'mother_tongue',
        'ip_ethnic_group',
        'religion',
        'house_street_sitio_purok',
        'barangay',
        'municipality_city',
        'province',
        'father_first_name',
        'father_last_name',
        'mother_maiden_first_name',
        'mother_maiden_middle_name',
        'mother_maiden_last_name',
        'guardian_name',
        'guardian_relationship',
        'contact_number',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(
            Section::class,
            'section_student'
        )->withTimestamps();
    }
}
