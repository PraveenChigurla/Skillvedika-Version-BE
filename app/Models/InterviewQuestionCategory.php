<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewQuestionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'show',
    ];

    protected $casts = [
        'show' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(InterviewQuestion::class, 'category_id');
    }

    public function getQuestionCountAttribute()
    {
        return $this->questions()->count();
    }
}

