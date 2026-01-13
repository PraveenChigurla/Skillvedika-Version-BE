<?php

namespace App\Http\Controllers;

use App\Models\InterviewQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterviewQuestionController extends Controller
{
    /**
     * Create a new question
     * POST /interview-questions
     */
    public function store(Request $request)
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionController::store error', [
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
     * Update a question
     * PUT /interview-questions/{id}
     */
    public function update(Request $request, $id)
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionController::update error', [
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
     * Delete a question
     * DELETE /interview-questions/{id}
     */
    public function destroy($id)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('InterviewQuestionController::destroy error', [
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

