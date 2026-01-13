<?php

namespace App\Http\Controllers;

use App\Models\InterviewQuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterviewQuestionCategoryController extends Controller
{
    /**
     * Get all categories for public (only visible ones with question count)
     * GET /interview-question-categories
     */
    public function index()
    {
        try {
            $categories = InterviewQuestionCategory::where('show', true)
                ->withCount('questions')
                ->orderBy('id', 'DESC')
                ->get()
                ->map(function ($category) {
                    $category->question_count = $category->questions_count;
                    unset($category->questions_count);
                    return $category;
                });

            return response()->json([
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionCategoryController::index error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all categories for admin (with question count)
     * GET /interview-question-categories-admin
     */
    public function indexAdmin()
    {
        try {
            $categories = InterviewQuestionCategory::withCount('questions')
                ->orderBy('id', 'DESC')
                ->get()
                ->map(function ($category) {
                    $category->question_count = $category->questions_count;
                    unset($category->questions_count);
                    return $category;
                });

            return response()->json([
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionCategoryController::indexAdmin error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get questions for a specific category
     * GET /interview-question-categories/{id}/questions
     */
    public function getQuestions($id)
    {
        try {
            $category = InterviewQuestionCategory::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found'
                ], 404);
            }

            $questions = $category->questions()
                ->orderBy('order', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();

            return response()->json([
                'data' => $questions
            ], 200);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionCategoryController::getQuestions error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new category
     * POST /interview-question-categories
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:interview_question_categories,slug',
                'description' => 'nullable|string',
                'show' => 'boolean',
            ]);

            $category = InterviewQuestionCategory::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'show' => $request->show ?? true,
            ]);

            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionCategoryController::store error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a category
     * PUT /interview-question-categories/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $category = InterviewQuestionCategory::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found'
                ], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:interview_question_categories,slug,' . $id,
                'description' => 'nullable|string',
                'show' => 'boolean',
            ]);

            $category->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'show' => $request->show ?? $category->show,
            ]);

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionCategoryController::update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a category
     * DELETE /interview-question-categories/{id}
     */
    public function destroy($id)
    {
        try {
            $category = InterviewQuestionCategory::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found'
                ], 404);
            }

            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionCategoryController::destroy error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

