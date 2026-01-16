<?php

namespace App\Http\Controllers;

use App\Models\FormDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormDetailsController extends Controller
{
    private const ERROR_FORM_DETAILS_NOT_FOUND = 'Form details not found.';

    /**
     * Get latest form details (USED BY WEBSITE FOOTER & HELP BUTTON)
     * GET /api/form-details
     */
    public function index()
    {
        try {
            $form = FormDetail::orderBy('id', 'desc')->first();

            return response()->json([
                'success' => true,
                'data' => $form, // can be null, frontend handles it safely
            ], 200);

        } catch (\Throwable $e) {
            // Log error but DO NOT break frontend
            Log::error('FormDetailsController@index failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // IMPORTANT: return 200 so UI never crashes
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Form details temporarily unavailable',
            ], 200);
        }
    }

    /**
     * Store new form details
     * POST /api/form-details
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'form_title'            => 'nullable|string',
                'form_subtitle'         => 'nullable|string',
                'full_name_label'       => 'nullable|string',
                'full_name_placeholder' => 'nullable|string',
                'email_label'           => 'nullable|string',
                'email_placeholder'     => 'nullable|string',
                'course_label'          => 'nullable|string',
                'course_placeholder'    => 'nullable|string',
                'terms_prefix'          => 'nullable|string',
                'terms_label'           => 'nullable|string',
                'terms_link'            => 'nullable|string',
                'submit_button_text'    => 'nullable|string',
                'form_footer_text'      => 'nullable|string',
            ]);

            $form = FormDetail::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Form details created successfully.',
                'data' => $form,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('FormDetailsController@store failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to create form details',
            ], 500);
        }
    }

    /**
     * Show specific form details
     * GET /api/form-details/{id}
     */
    public function show($id)
    {
        try {
            $form = FormDetail::find($id);

            if (!$form) {
                return response()->json([
                    'success' => false,
                    'message' => self::ERROR_FORM_DETAILS_NOT_FOUND,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $form,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('FormDetailsController@show failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch form details',
            ], 500);
        }
    }

    /**
     * Update form details
     * PUT /api/form-details/{id?}
     */
    public function update(Request $request, $id = null)
    {
        try {
            $form = $id
                ? FormDetail::find($id)
                : FormDetail::orderBy('id', 'desc')->first();

            if (!$form) {
                return response()->json([
                    'success' => false,
                    'message' => self::ERROR_FORM_DETAILS_NOT_FOUND,
                ], 404);
            }

            $validated = $request->validate([
                'form_title'            => 'nullable|string',
                'form_subtitle'         => 'nullable|string',
                'full_name_label'       => 'nullable|string',
                'full_name_placeholder' => 'nullable|string',
                'email_label'           => 'nullable|string',
                'email_placeholder'     => 'nullable|string',
                'course_label'          => 'nullable|string',
                'course_placeholder'    => 'nullable|string',
                'terms_prefix'          => 'nullable|string',
                'terms_label'           => 'nullable|string',
                'terms_link'            => 'nullable|string',
                'submit_button_text'    => 'nullable|string',
                'form_footer_text'      => 'nullable|string',
            ]);

            $form->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Form details updated successfully.',
                'data' => $form,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('FormDetailsController@update failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to update form details',
            ], 500);
        }
    }

    /**
     * Delete form details
     * DELETE /api/form-details/{id?}
     */
    public function destroy($id = null)
    {
        try {
            $form = $id
                ? FormDetail::find($id)
                : FormDetail::orderBy('id', 'desc')->first();

            if (!$form) {
                return response()->json([
                    'success' => false,
                    'message' => self::ERROR_FORM_DETAILS_NOT_FOUND,
                ], 404);
            }

            $form->delete();

            return response()->json([
                'success' => true,
                'message' => 'Form details deleted successfully.',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('FormDetailsController@destroy failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to delete form details',
            ], 500);
        }
    }
}
