<?php

namespace App\Http\Controllers;

use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SeoController extends Controller
{
    /**
     * GET /api/seo - List all SEO entries
     * GET /api/seo?slug=:slug - Get SEO by slug (for frontend)
     *
     * For frontend requests: Returns fallback metadata if slug not found (SEO safety)
     * For admin panel: Returns all SEO entries
     */
    public function index(Request $request)
    {
        // If slug query parameter is provided, return single record
        if ($request->has('slug')) {
            $slug = $request->query('slug');
            $seo = Seo::findBySlug($slug);

            if (!$seo) {
                // Return fallback metadata instead of 404 (SEO safety)
                // Prevents empty <title> tags and ensures Google-safe responses
                return response()->json([
                    'data' => [
                        'slug' => $slug,
                        'page_name' => ucfirst(str_replace('-', ' ', $slug)),
                        'meta_title' => 'SkillVedika',
                        'meta_description' => 'Industry-ready learning platform',
                        'meta_keywords' => 'SkillVedika, IT Training, Corporate Training'
                    ]
                ], 200);
            }

            return response()->json(['data' => $seo]);
        }

        // Otherwise, return all SEO rows (for admin panel)
        $seos = Seo::orderBy('page_name', 'asc')->get();
        return response()->json([
            'data' => $seos
        ]);
    }

    /**
     * GET /api/seo/{slug} - Get SEO by slug (alternative endpoint)
     * Returns fallback metadata if slug not found (SEO safety)
     */
    public function show($slug)
    {
        $seo = Seo::findBySlug($slug);

        if (!$seo) {
            // Return fallback metadata instead of 404 (SEO safety)
            // Prevents empty <title> tags and ensures Google-safe responses
            return response()->json([
                'data' => [
                    'slug' => $slug,
                    'page_name' => ucfirst(str_replace('-', ' ', $slug)),
                    'meta_title' => 'SkillVedika',
                    'meta_description' => 'Industry-ready learning platform',
                    'meta_keywords' => 'SkillVedika, IT Training, Corporate Training'
                ]
            ], 200);
        }

        return response()->json(['data' => $seo]);
    }

    /**
     * POST /api/seo - Create new SEO entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:seos,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'page_name' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // Ensure slug is lowercase and properly formatted
        $validated['slug'] = Str::slug($validated['slug']);

        $seo = Seo::create($validated);

        return response()->json([
            'message' => 'SEO created successfully',
            'data' => $seo
        ], 201);
    }

    /**
     * PATCH /api/seo/{slug} - Update SEO entry (partial update)
     * Only updates provided fields, preserves existing data
     * Slug cannot be changed after creation
     */
    public function update(Request $request, $slug)
    {
        $seo = Seo::findBySlug($slug);

        if (!$seo) {
            return response()->json(['message' => 'SEO page not found'], 404);
        }

        $validated = $request->validate([
            'meta_title' => 'sometimes|nullable|string|max:255',
            'meta_description' => 'sometimes|nullable|string',
            'meta_keywords' => 'sometimes|nullable|string',
            'page_name' => 'sometimes|nullable|string|max:255',
            // Explicitly prevent slug updates
            'slug' => 'prohibited',
        ]);

        // Only update provided fields
        $seo->update($validated);

        return response()->json([
            'message' => 'SEO updated successfully',
            'data' => $seo->fresh()
        ]);
    }

    /**
     * DELETE /api/seo/{slug} - Delete SEO entry
     * Protected slugs cannot be deleted (critical pages)
     */
    public function destroy($slug)
    {
        // Protect critical pages from deletion
        $protectedSlugs = [
            'home',
            'privacy-policy',
            'terms-and-conditions',
            'terms-and-conditions-instructor',
            'about-us',
            'contact-us',
        ];

        if (in_array($slug, $protectedSlugs)) {
            return response()->json([
                'message' => 'This SEO page cannot be deleted. It is a protected system page.'
            ], 403);
        }

        $seo = Seo::findBySlug($slug);

        if (!$seo) {
            return response()->json(['message' => 'SEO page not found'], 404);
        }

        $seo->delete();

        return response()->json([
            'message' => 'SEO deleted successfully'
        ]);
    }
}
