<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorApplication extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'years_of_experience',
        'skills',
        'message',
    ];

    protected $casts = [
        'skills' => 'array',
    ];
}
