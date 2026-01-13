<?php

namespace App\Http\Controllers;

use App\Models\BecomeInstructorContent;
use Illuminate\Http\Request;

class BecomeInstructorContentController extends Controller
{
    /**
     * GET /api/become-instructor
     * Returns the become instructor page content
     */
    public function show()
    {
        $content = BecomeInstructorContent::first();

        if (!$content) {
            return response()->json([
                'message' => 'Content not found'
            ], 404);
        }

        return response()->json([
            'data' => $content
        ], 200);
    }

    /**
     * POST /api/become-instructor
     * Create or Update content (upsert pattern)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Hero Section
            'hero_title' => 'nullable|string',
            'hero_description' => 'nullable|string',
            'hero_button_text' => 'nullable|string|max:255',
            'hero_image' => 'nullable|string|max:255',

            // Benefits Section
            'benefits_title' => 'nullable|string',
            'benefits_subtitle' => 'nullable|string',
            'benefits' => 'nullable|array',
            'benefits.*.title' => 'required_with:benefits|string',
            'benefits.*.description' => 'required_with:benefits|string',
            'benefits.*.icon' => 'nullable|string',
            'benefits.*.highlighted' => 'nullable|boolean',

            // CTA Section
            'cta_title' => 'nullable|string',
            'cta_description' => 'nullable|string',
            'cta_button_text' => 'nullable|string|max:255',

            // Legacy fields (for backward compatibility)
            'heading' => 'nullable|string',
            'content' => 'nullable|string',
            'form_title' => 'nullable|string',
            'banner' => 'nullable|string',
        ]);

        // Use updateOrCreate to handle both create and update
        $content = BecomeInstructorContent::updateOrCreate(
            ['id' => 1], // Assuming single record
            $validated
        );

        return response()->json([
            'message' => 'Content saved successfully',
            'data' => $content
        ], 201);
    }

    /**
     * PUT/PATCH /api/become-instructor/{id?}
     * Update existing content
     */
    public function update(Request $request, $id = null)
    {
        $content = BecomeInstructorContent::first();

        if (!$content) {
            return response()->json([
                'message' => 'Content not found'
            ], 404);
        }

        $validated = $request->validate([
            // Hero Section
            'hero_title' => 'sometimes|nullable|string',
            'hero_description' => 'sometimes|nullable|string',
            'hero_button_text' => 'sometimes|nullable|string|max:255',
            'hero_image' => 'sometimes|nullable|string|max:255',

            // Benefits Section
            'benefits_title' => 'sometimes|nullable|string',
            'benefits_subtitle' => 'sometimes|nullable|string',
            'benefits' => 'sometimes|nullable|array',
            'benefits.*.title' => 'required_with:benefits|string',
            'benefits.*.description' => 'required_with:benefits|string',
            'benefits.*.icon' => 'nullable|string',
            'benefits.*.highlighted' => 'nullable|boolean',

            // CTA Section
            'cta_title' => 'sometimes|nullable|string',
            'cta_description' => 'sometimes|nullable|string',
            'cta_button_text' => 'sometimes|nullable|string|max:255',

            // Legacy fields
            'heading' => 'sometimes|nullable|string',
            'content' => 'sometimes|nullable|string',
            'form_title' => 'sometimes|nullable|string',
            'banner' => 'sometimes|nullable|string',
        ]);

        $content->update($validated);

        return response()->json([
            'message' => 'Content updated successfully',
            'data' => $content->fresh()
        ], 200);
    }

    /**
     * DELETE /api/become-instructor/{id?}
     * Delete content (optional - usually not needed for single record)
     */
    public function destroy($id = null)
    {
        $content = BecomeInstructorContent::first();

        if (!$content) {
            return response()->json([
                'message' => 'Content not found'
            ], 404);
        }

        $content->delete();

        return response()->json([
            'message' => 'Content deleted successfully'
        ], 200);
    }
}
