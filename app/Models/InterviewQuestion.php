<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'question',
        'answer',
        'order',
        'show',
    ];

    protected $casts = [
        'show' => 'boolean',
        'order' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(InterviewQuestionCategory::class, 'category_id');
    }
}

