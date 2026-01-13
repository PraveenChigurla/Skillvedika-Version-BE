<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InstructorApplication;
use Illuminate\Support\Facades\Log;

class InstructorApplicationController extends Controller
{
    // Constants
    private const MAX_LIMIT = 100;
    private const DEFAULT_LIMIT = 20;
    private const DEFAULT_PAGE = 1;

    // POST: Website form submit
    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'yearsOfExperience' => 'required|string',
            'skills' => 'required|array|min:1',
            'message' => 'nullable|string',
        ]);

        $application = InstructorApplication::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'years_of_experience' => $request->yearsOfExperience,
            'skills' => $request->skills,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'data' => $application,
        ], 201);
    }

    /**
     * Apply search filter to query
     */
    private function applySearchFilter($query, $search)
    {
        if ($search === '') {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('years_of_experience', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%")
              ->orWhereJsonContains('skills', $search);
        });
    }

    /**
     * Apply experience filter to query
     */
    private function applyExperienceFilter($query, $experience)
    {
        if ($experience !== '') {
            $query->where('years_of_experience', $experience);
        }
    }

    /**
     * Apply date filters to query
     */
    private function applyDateFilters($query, $dateFrom, $dateTo)
    {
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
    }

    /**
     * Validate and normalize sort parameters
     */
    private function normalizeSortParams($request)
    {
        $sortBy = $request->get('sort_by', 'id');
        $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'years_of_experience',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'id';
        }

        return ['sortBy' => $sortBy, 'sortDir' => $sortDir];
    }

    // GET: Admin list with pagination, filtering, and sorting
    public function index(Request $request)
    {
        try {
            // Basic params
            $limit = max(1, min(self::MAX_LIMIT, (int) $request->get('limit', self::DEFAULT_LIMIT)));
            $page = max(1, (int) $request->get('page', self::DEFAULT_PAGE));
            $search = trim((string) $request->get('search', ''));
            $experience = trim((string) $request->get('experience', ''));
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            // Normalize sort parameters
            $sortParams = $this->normalizeSortParams($request);

            // Base query
            $query = InstructorApplication::query();

            // Apply filters
            $this->applySearchFilter($query, $search);
            $this->applyExperienceFilter($query, $experience);
            $this->applyDateFilters($query, $dateFrom, $dateTo);

            // Sort
            $query->orderBy($sortParams['sortBy'], $sortParams['sortDir']);

            // Pagination
            $request->query->set('page', (string)$page);
            $paginator = $query->paginate($limit, ['*'], 'page', $page);

            $actualPage = $paginator->currentPage();

            return response()->json([
                'data' => $paginator->items(),
                'current_page' => $actualPage,
                'last_page' => max(1, $paginator->lastPage()),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[InstructorApplicationController] Index ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to load applications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // GET: Admin show single
    public function show($id)
    {
        $application = InstructorApplication::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $application
        ]);
    }

    // DELETE: Admin delete
    public function destroy($id)
    {
        $application = InstructorApplication::findOrFail($id);
        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application deleted successfully'
        ]);
    }
}
