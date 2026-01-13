<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewQuestionsPageContent extends Model
{
    use HasFactory;

    protected $table = 'interview_questions_page_contents';

    protected $fillable = [
        'hero_title',
        'hero_description',
    ];
}

