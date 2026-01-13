<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterviewQuestionsPageContent;
use Illuminate\Support\Facades\Log;

class InterviewQuestionsPageContentController extends Controller
{
    /**
     * Get the latest page content
     * GET /interview-questions-page
     */
    public function show()
    {
        try {
            $data = InterviewQuestionsPageContent::orderBy('id', 'desc')->first();

            return response()->json([
                'success' => true,
                'data' => $data ?? (object)[]
            ]);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionsPageContentController::show error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the latest page content (alias for show)
     * GET /interview-questions-page
     */
    public function index()
    {
        return $this->show();
    }

    /**
     * Create new page content
     * POST /interview-questions-page
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'hero_title' => 'nullable|string|max:255',
                'hero_description' => 'nullable|string',
            ]);

            $record = InterviewQuestionsPageContent::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Interview questions page content created successfully',
                'data' => $record
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionsPageContentController::store error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update page content
     * PUT /interview-questions-page/{id?}
     */
    public function update(Request $request, $id = null)
    {
        try {
            $data = $request->validate([
                'hero_title' => 'nullable|string|max:255',
                'hero_description' => 'nullable|string',
            ]);

            $record = $id 
                ? InterviewQuestionsPageContent::find($id) 
                : InterviewQuestionsPageContent::orderBy('id', 'desc')->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not found'
                ], 404);
            }

            $filtered = array_filter($data, function ($v) { 
                return !is_null($v); 
            });

            $record->update($filtered);

            return response()->json([
                'success' => true,
                'message' => 'Interview questions page content updated successfully',
                'data' => $record
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionsPageContentController::update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete page content
     * DELETE /interview-questions-page/{id?}
     */
    public function destroy($id = null)
    {
        try {
            $record = $id 
                ? InterviewQuestionsPageContent::find($id) 
                : InterviewQuestionsPageContent::orderBy('id', 'desc')->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not found'
                ], 404);
            }

            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('InterviewQuestionsPageContentController::destroy error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

