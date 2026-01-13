<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Seo extends Model
{
    protected $fillable = [
        'slug',
        'page_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Boot the model and add slug immutability protection
     * Even if someone bypasses controller validation, slug stays protected
     */
    protected static function booted()
    {
        static::updating(function ($seo) {
            if ($seo->isDirty('slug')) {
                throw ValidationException::withMessages([
                    'slug' => ['Slug cannot be updated. It is immutable after creation.']
                ]);
            }
        });
    }

    /**
     * Get the SEO record by slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->first();
    }
}
