<?php

namespace App\Http\Controllers;

use App\Models\InterviewQuestion;
use App\Models\InterviewQuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterviewQuestionController extends Controller
{
    /**
     * GET /api/interview-questions/{slug}
     * Fetch questions by category slug (USED BY WEBSITE)
     */
    public function showBySlug($slug)
    {
        try {
            $category = InterviewQuestionCategory::where('slug', $slug)
                ->where('show', true)
                ->first();

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found'
                ], 404);
            }

            $questions = InterviewQuestion::where('category_id', $category->id)
                ->where('show', true)
                ->orderBy('order')
                ->get();

            return response()->json([
                'category' => $category,
                'questions' => $questions,
            ], 200);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionController::showBySlug error', [
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
     * POST /api/interview-questions
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:interview_question_categories,id',
            'question' => 'required|string',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
            'show' => 'boolean',
        ]);

        $question = InterviewQuestion::create([
            'category_id' => $request->category_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
            'show' => $request->show ?? true,
        ]);

        return response()->json([
            'message' => 'Question created successfully',
            'data' => $question
        ], 201);
    }

    /**
     * PUT /api/interview-questions/{id}
     */
    public function update(Request $request, $id)
    {
        $question = InterviewQuestion::find($id);

        if (!$question) {
            return response()->json([
                'message' => 'Question not found'
            ], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:interview_question_categories,id',
            'question' => 'required|string',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
            'show' => 'boolean',
        ]);

        $question->update([
            'category_id' => $request->category_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? $question->order,
            'show' => $request->show ?? $question->show,
        ]);

        return response()->json([
            'message' => 'Question updated successfully',
            'data' => $question
        ], 200);
    }

    /**
     * DELETE /api/interview-questions/{id}
     */
    public function destroy($id)
    {
        $question = InterviewQuestion::find($id);

        if (!$question) {
            return response()->json([
                'message' => 'Question not found'
            ], 404);
        }

        $question->delete();

        return response()->json([
            'message' => 'Question deleted successfully'
        ], 200);
    }
}
