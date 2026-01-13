<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BecomeInstructorContent extends Model
{
    protected $table = 'become_instructor_contents';

    protected $fillable = [
        // Hero Section
        'hero_title',
        'hero_description',
        'hero_button_text',
        'hero_image',

        // Benefits Section
        'benefits_title',
        'benefits_subtitle',
        'benefits',

        // CTA Section
        'cta_title',
        'cta_description',
        'cta_button_text',

        // Legacy fields (for backward compatibility)
        'heading',
        'content',
        'banner',
        'form_title',
    ];

    protected $casts = [
        // Only cast benefits to array (it's a JSON array of objects)
        'benefits' => 'array',
        // Titles and descriptions are stored as strings (HTML content)
        // If you need structured content later, change to JSON type in migration
    ];
}
