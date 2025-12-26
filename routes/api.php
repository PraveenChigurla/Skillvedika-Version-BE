<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\AdminPasswordController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\PopularTagController;
use App\Http\Controllers\CoursePageContentController;
use App\Http\Controllers\CorporateTrainingController;
use App\Http\Controllers\LiveDemoController;
use App\Http\Controllers\OnJobSupportContentController;
use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\ContactPageController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\BlogPageController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseDetailsController;
use App\Http\Controllers\CourseDetailsJobAssistanceController;

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HrFaqController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\EnrollmentController;

// Header settings controller
use App\Http\Controllers\HeaderSettingController;

// ⭐ Add Terms & Conditions Controller
use App\Http\Controllers\TermsAndConditionsController;

use App\Http\Controllers\FooterSettingsController;
use App\Http\Controllers\FormDetailsController;
use App\Http\Controllers\JobAssistanceProgramController;
use App\Http\Controllers\PlacementsReserveController;
use App\Http\Controllers\SearchController;



/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No Authentication)
|--------------------------------------------------------------------------
*/

Route::get('/ping', fn() => response()->json(['ok' => true]));

Route::get('/categories', [CategoryController::class, 'index']);
// Make Categories CRUD public for easier local development (admin UI)
// NOTE: There is also a protected BlogCategoryController using the same '/categories' path for blog categories.
// To avoid 401s during development, expose the general CategoryController endpoints as public fallbacks.
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
// Legacy /public prefixed aliases
Route::post('/public/categories', [CategoryController::class, 'store']);
Route::put('/public/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/public/categories/{id}', [CategoryController::class, 'destroy']);
Route::get('/skills', [SkillController::class, 'index']);
Route::get('/popular-tags', [PopularTagController::class, 'index']);
// Allow adding tags during local development (proxied requests) — make POST public so the admin UI can create tags without session if CSRF issues persist.
// TODO: move back under auth:sanctum for production.
// Make Popular Tags CRUD public for easier local development (admin UI)
Route::post('/popular-tags', [PopularTagController::class, 'store']);
Route::put('/popular-tags/{id}', [PopularTagController::class, 'update']);
Route::delete('/popular-tags/{id}', [PopularTagController::class, 'destroy']);
// Keep legacy /public prefixed endpoints for minimal-change clients
Route::post('/public/popular-tags', [PopularTagController::class, 'store']);
Route::put('/public/popular-tags/{id}', [PopularTagController::class, 'update']);
Route::delete('/public/popular-tags/{id}', [PopularTagController::class, 'destroy']);

// Make Skills CRUD public for easier local development (admin UI)
Route::post('/skills', [SkillController::class, 'store']);
Route::put('/skills/{id}', [SkillController::class, 'update']);
Route::delete('/skills/{id}', [SkillController::class, 'destroy']);
// Keep legacy /public prefixed endpoint for minimal-change clients
Route::post('/public/skills', [SkillController::class, 'store']);
Route::put('/public/skills/{id}', [SkillController::class, 'update']);
Route::delete('/public/skills/{id}', [SkillController::class, 'destroy']);

// Debugging helper: return incoming request headers, cookies and auth state
// Use this to confirm what the backend receives from the browser/proxy.
Route::get('/debug/request', function (Request $request) {
    try {
        $headers = [];
        foreach ($request->headers->all() as $k => $v) {
            $headers[$k] = $v;
        }

        $cookies = [];
        foreach ($request->cookies->all() as $k => $v) {
            $cookies[$k] = $v;
        }

        return response()->json([
            'ok' => true,
            'headers' => $headers,
            'cookies' => $cookies,
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::get('/blog-categories', [BlogCategoryController::class, 'index']);

Route::get('/settings', [SettingsController::class, 'get']);
// Make settings update public for easier local development (admin UI)
// TODO: move back under auth:sanctum for production.
Route::post('/settings', [SettingsController::class, 'update']);

// Header settings (public for local dev)
Route::get('/header-settings', [HeaderSettingController::class, 'index']);
Route::get('/header-settings/{id}', [HeaderSettingController::class, 'show']);
Route::post('/header-settings', [HeaderSettingController::class, 'store']);
Route::put('/header-settings', [HeaderSettingController::class, 'update']); // Update latest
Route::put('/header-settings/{id}', [HeaderSettingController::class, 'update']); // Update specific
Route::patch('/header-settings/{id}', [HeaderSettingController::class, 'update']);
Route::delete('/header-settings/{id}', [HeaderSettingController::class, 'destroy']);

// Respond to preflight OPTIONS requests
Route::options('/header-settings', function () { return response('', 200); });

// Footer settings (public for local dev)
Route::get('/footer-settings', [FooterSettingsController::class, 'index']);
Route::get('/footer-settings/{id}', [FooterSettingsController::class, 'show']);
Route::post('/footer-settings', [FooterSettingsController::class, 'store']);
Route::put('/footer-settings', [FooterSettingsController::class, 'update']); // Update latest
Route::put('/footer-settings/{id}', [FooterSettingsController::class, 'update']); // Update specific
Route::patch('/footer-settings/{id}', [FooterSettingsController::class, 'update']);
Route::delete('/footer-settings/{id}', [FooterSettingsController::class, 'destroy']);
Route::options('/footer-settings', function () { return response('', 200); });

Route::get('/homepage', [HomepageController::class, 'index']);
Route::post('/homepage', [HomepageController::class, 'store']);
Route::put('/homepage/{id?}', [HomepageController::class, 'update']);
Route::patch('/homepage/{id?}', [HomepageController::class, 'update']);
Route::delete('/homepage/{id?}', [HomepageController::class, 'destroy']);

Route::get('/course-page-content', [CoursePageContentController::class, 'get']);
Route::post('/course-page-content', [CoursePageContentController::class, 'store']);
Route::put('/course-page-content/{id?}', [CoursePageContentController::class, 'update']);
Route::patch('/course-page-content/{id?}', [CoursePageContentController::class, 'update']);
Route::delete('/course-page-content/{id?}', [CoursePageContentController::class, 'destroy']);

// Job Assistance program content (page-specific table)
Route::get('/course-details/job-assistance', [CourseDetailsJobAssistanceController::class, 'index']);
Route::get('/course-details/job-assistance/{id}', [CourseDetailsJobAssistanceController::class, 'show']);
Route::post('/course-details/job-assistance', [CourseDetailsJobAssistanceController::class, 'store']);
Route::put('/course-details/job-assistance/{id}', [CourseDetailsJobAssistanceController::class, 'update']);

Route::post('/live-demo', [LiveDemoController::class, 'store']);
Route::get('/live-demo', [LiveDemoController::class, 'show']);

Route::get('/on-job-support', [OnJobSupportContentController::class, 'show']);
Route::get('/on-job-support-page', [OnJobSupportContentController::class, 'show']);
Route::post('/on-job-support', [OnJobSupportContentController::class, 'store']);
Route::put('/on-job-support/{id?}', [OnJobSupportContentController::class, 'update']);
Route::patch('/on-job-support/{id?}', [OnJobSupportContentController::class, 'update']);
Route::delete('/on-job-support/{id?}', [OnJobSupportContentController::class, 'destroy']);

Route::get('/corporate-training', [CorporateTrainingController::class, 'show']);
Route::post('/corporate-training/update-latest', [CorporateTrainingController::class, 'updateLatest']); // Reference format

Route::get('/about-page', [AboutPageController::class, 'show']);
Route::post('/about-page', [AboutPageController::class, 'store']);
Route::put('/about-page/{id?}', [AboutPageController::class, 'update']);
Route::patch('/about-page/{id?}', [AboutPageController::class, 'update']);
Route::delete('/about-page/{id?}', [AboutPageController::class, 'destroy']);

Route::get('/contact-page', [ContactPageController::class, 'index']);
Route::post('/contact-page', [ContactPageController::class, 'store']);
Route::post('/contact-page/update', [ContactPageController::class, 'update']); // Legacy route
Route::put('/contact-page/{id?}', [ContactPageController::class, 'update']);
Route::patch('/contact-page/{id?}', [ContactPageController::class, 'update']);
Route::delete('/contact-page/{id?}', [ContactPageController::class, 'destroy']);

Route::get('/seo', [SeoController::class, 'index']);
Route::get('/seo/{id}', [SeoController::class, 'show']);
Route::post('/seo', [SeoController::class, 'store']);
Route::post('/seo/{id}', [SeoController::class, 'update']); // Legacy route
Route::put('/seo/{id}', [SeoController::class, 'update']);
Route::patch('/seo/{id}', [SeoController::class, 'update']);
Route::delete('/seo/{id}', [SeoController::class, 'destroy']);

Route::get('/blog-page', [BlogPageController::class, 'index']);
Route::post('/blog-page', [BlogPageController::class, 'store']);
Route::post('/blog-page/update', [BlogPageController::class, 'update']); // Legacy route
Route::put('/blog-page/{id?}', [BlogPageController::class, 'update']);
Route::patch('/blog-page/{id?}', [BlogPageController::class, 'update']);
Route::delete('/blog-page/{id?}', [BlogPageController::class, 'destroy']);

Route::get('/leads', [EnrollmentController::class, 'index']);
Route::get('/leads/export', [EnrollmentController::class, 'export']);
Route::get('/leads/{id}', [EnrollmentController::class, 'show']);
// Public enrollment form submission - frontend uses /enroll
Route::post('/enroll', [EnrollmentController::class, 'store']);
Route::put('/leads/{id}/status', [EnrollmentController::class, 'updateStatus']);
Route::delete('/leads/{id}', [EnrollmentController::class, 'destroy']);
Route::post('/leads/delete-multiple', [EnrollmentController::class, 'deleteMultiple']);

Route::get('/form-details', [FormDetailsController::class, 'index']);
Route::get('/form-details/{id}', [FormDetailsController::class, 'show']);
Route::post('/form-details', [FormDetailsController::class, 'store']);
Route::put('/form-details/{id}', [FormDetailsController::class, 'update']);
Route::delete('/form-details/{id}', [FormDetailsController::class, 'destroy']);

Route::get('/job-assistance', [JobAssistanceProgramController::class, 'index']);
Route::get('/job-assistance/{id}', [JobAssistanceProgramController::class, 'show']);
Route::post('/job-assistance', [JobAssistanceProgramController::class, 'store']);
Route::put('/job-assistance/{id}', [JobAssistanceProgramController::class, 'update']);
Route::delete('/job-assistance/{id}', [JobAssistanceProgramController::class, 'destroy']);

Route::get('/placements-reserve', [PlacementsReserveController::class, 'index']);
Route::get('/placements-reserve/{id}', [PlacementsReserveController::class, 'show']);
Route::post('/placements-reserve', [PlacementsReserveController::class, 'store']);
Route::put('/placements-reserve/{id}', [PlacementsReserveController::class, 'update']);
Route::delete('/placements-reserve/{id}', [PlacementsReserveController::class, 'destroy']);

Route::post('/courses', [CourseController::class, 'store']);
Route::put('/courses/{id}', [CourseController::class, 'update']);
Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
// Course Details Routes - Support both reference format and current format
Route::get('/course-details', [CourseDetailsController::class, 'index']);
Route::get('/course-details/{id}', [CourseDetailsController::class, 'show']); // Support admin frontend and reference format
Route::post('/course-details', [CourseDetailsController::class, 'store']);
Route::put('/course-details/{id}', [CourseDetailsController::class, 'update']); // Supports both {id} and {courseId}
Route::patch('/course-details/{id}', [CourseDetailsController::class, 'update']);
Route::delete('/course-details/{id}', [CourseDetailsController::class, 'destroy']);

// SPA login must run under the web middleware so a session cookie can be created.
Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('web');
Route::post('/admin/forgot-password', [AdminPasswordController::class, 'forgot']);

// Logout should work even if user is not authenticated (idempotent operation)
// It will try to revoke tokens if authenticated, otherwise just clear the cookie
Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

// Authentication verification endpoint - THE SOURCE OF TRUTH
// Called by middleware on EVERY route access to verify current login status
// Protected by auth:sanctum - returns 401 if not authenticated
Route::get('/admin/me', [AdminController::class, 'me'])->middleware('auth:sanctum');

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
// Submit review for a course
Route::post('/courses/{id}/review', [ReviewController::class, 'store']);

// Search suggestions
Route::get('/search/suggestions', [SearchController::class, 'suggestions']);


/*
|--------------------------------------------------------------------------
| ⭐ PUBLIC BLOG ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);

// Make Blogs CRUD public for easier local development (admin UI)
// TODO: move these back under auth:sanctum for production.
Route::post('/blogs', [BlogController::class, 'store']);
Route::put('/blogs/{id}', [BlogController::class, 'update']);
Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
// Keep legacy /public prefixed endpoints for minimal-change clients
Route::post('/public/blogs', [BlogController::class, 'store']);
Route::put('/public/blogs/{id}', [BlogController::class, 'update']);
Route::delete('/public/blogs/{id}', [BlogController::class, 'destroy']);


/*
|--------------------------------------------------------------------------
| ⭐ PUBLIC FAQ ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/faqs/{id}', [FaqController::class, 'show']);


/*
|--------------------------------------------------------------------------
| ⭐ PUBLIC Terms & Conditions ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/terms-and-conditions', [TermsAndConditionsController::class, 'show']);
Route::get('/terms', [TermsAndConditionsController::class, 'show']); // Alias
Route::get('/terms/all', [TermsAndConditionsController::class, 'index']);



/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (auth:sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/admin/profile', [AdminController::class, 'profile']);
    Route::post('/admin/update', [AdminController::class, 'update']);

    Route::post('/settings/update', [SettingsController::class, 'update']);

    Route::delete('/leads/{id}', [EnrollmentController::class, 'destroy']);
    Route::post('/leads/delete-multiple', [EnrollmentController::class, 'deleteMultiple']);
    Route::put('/leads/{id}/status', [EnrollmentController::class, 'updateStatus']);

    Route::post('/corporate-training', [CorporateTrainingController::class, 'store']);
    Route::put('/corporate-training/{id?}', [CorporateTrainingController::class, 'update']);
    Route::patch('/corporate-training/{id?}', [CorporateTrainingController::class, 'update']);
    Route::delete('/corporate-training/{id?}', [CorporateTrainingController::class, 'destroy']);
    Route::post('/course-page-content/update', [CoursePageContentController::class, 'update']);


    /*
    |--------------------------------------------------------------------------
    | ⭐ BLOG CATEGORY CRUD
    |--------------------------------------------------------------------------
    */
    Route::post('/categories', [BlogCategoryController::class, 'store']);
    Route::put('/categories/{id}', [BlogCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [BlogCategoryController::class, 'destroy']);


    /*
    |--------------------------------------------------------------------------
    | ⭐ BLOG CRUD
    |--------------------------------------------------------------------------
    */
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | ⭐ POPULAR TAGS CRUD (protected)
    |--------------------------------------------------------------------------
    */
    Route::post('/popular-tags', [PopularTagController::class, 'store']);
    Route::put('/popular-tags/{id}', [PopularTagController::class, 'update']);
    Route::delete('/popular-tags/{id}', [PopularTagController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | ⭐ SKILLS CRUD (protected)
    |--------------------------------------------------------------------------
    */
    Route::post('/skills', [SkillController::class, 'store']);
    Route::put('/skills/{id}', [SkillController::class, 'update']);
    Route::delete('/skills/{id}', [SkillController::class, 'destroy']);


    /*
    |--------------------------------------------------------------------------
    | ⭐ FAQ CRUD
    |--------------------------------------------------------------------------
    */
    Route::post('/faqs', [FaqController::class, 'store']);
    Route::put('/faqs/{id}', [FaqController::class, 'update']);
    Route::delete('/faqs/{id}', [FaqController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | ⭐ HR FAQ CRUD
    |--------------------------------------------------------------------------
    */
    Route::post('/hr-faqs', [HrFaqController::class, 'store']);
    Route::put('/hr-faqs/{id}', [HrFaqController::class, 'update']);
    Route::patch('/hr-faqs/{id}', [HrFaqController::class, 'update']);
    Route::delete('/hr-faqs/{id}', [HrFaqController::class, 'destroy']);


    /*
    |--------------------------------------------------------------------------
    | ⭐ Terms & Conditions CRUD (PROTECTED)
    |--------------------------------------------------------------------------
    */
    Route::post('/terms-and-conditions', [TermsAndConditionsController::class, 'store']);
    Route::post('/terms', [TermsAndConditionsController::class, 'store']); // Alias
    Route::put('/terms/{id?}', [TermsAndConditionsController::class, 'update']);
    Route::patch('/terms/{id?}', [TermsAndConditionsController::class, 'patch']);
    Route::delete('/terms/{id}', [TermsAndConditionsController::class, 'destroy']);
});

