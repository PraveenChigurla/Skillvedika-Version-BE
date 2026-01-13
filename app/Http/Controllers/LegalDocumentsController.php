<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TermsAndConditions;

class LegalDocumentsController extends Controller
{
    /**
     * GET /api/legal/{type}
     * type = student | instructor | privacy
     */
    public function show(string $type)
    {
        $this->validateType($type);

        $record = TermsAndConditions::where('type', $type)
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $record
        ]);
    }

    /**
     * GET /api/legal/{type}/all
     */
    public function index(string $type)
    {
        $this->validateType($type);

        $records = TermsAndConditions::where('type', $type)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }

    /**
     * POST /api/legal
     * Always creates a NEW version
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'nullable|string|max:255',
            'type'    => 'required|in:student,instructor,privacy',
            'content' => 'required',
        ]);

        $doc = TermsAndConditions::create([
            'title'           => $validated['title'] ?? ucfirst($validated['type']) . ' Terms & Conditions',
            'type'            => $validated['type'],
            'content'         => $validated['content'],
            'last_updated_on' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document created successfully',
            'data' => $doc,
        ], 201);
    }

    /**
     * PUT /api/legal/{id}
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'nullable',
        ]);

        $doc = TermsAndConditions::findOrFail($id);

        $doc->update([
            'title'           => $validated['title'] ?? $doc->title,
            'content'         => $validated['content'] ?? $doc->content,
            'last_updated_on' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'data' => $doc,
        ]);
    }

    /**
     * DELETE /api/legal/{id}
     */
    public function destroy(int $id)
    {
        TermsAndConditions::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully',
        ]);
    }

    /**
     * Allowed document types
     */
    private function validateType(string $type): void
    {
        if (!in_array($type, ['student', 'instructor', 'privacy'])) {
            abort(404, 'Invalid legal document type');
        }
    }
}
