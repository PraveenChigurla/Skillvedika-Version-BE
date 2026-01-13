<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    /**
     * PUBLIC – Website
     * Only active testimonials
     */
    public function index(Request $request)
    {
        $category = $request->query('category');

        $query = Testimonial::where('is_active', true);

        if ($category) {
            $query->where('course_category', 'like', "%{$category}%");
        }

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderBy('display_order')
                ->orderByDesc('created_at')
                ->get(),
        ])->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * ADMIN – Unlimited testimonials
     */
    public function adminIndex()
    {
        return response()->json([
            'success' => true,
            'data' => Testimonial::orderBy('display_order')
                ->orderByDesc('created_at')
                ->get(),
        ])->header('Cache-Control', 'no-store');
    }

    /**
     * CREATE
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $testimonial = Testimonial::create($data);

        return response()->json([
            'success' => true,
            'testimonial' => $testimonial,
        ], 201);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $data = $this->validateData($request);

        $testimonial->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial updated successfully',
            'testimonial' => $testimonial->fresh(),
        ]);
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        Testimonial::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully',
        ]);
    }

    /**
     * Shared validation
     */
    private function validateData(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'student_name' => 'required|string|max:255',
            'student_role' => 'nullable|string|max:255',
            'student_company' => 'nullable|string|max:255',
            'course_category' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'testimonial_text' => 'required|string|max:1000',
            'student_image' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        return $validator->validate();
    }
}
