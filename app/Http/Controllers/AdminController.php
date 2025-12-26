<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Verify current authentication status - THE SOURCE OF TRUTH
     * This endpoint is called by middleware on EVERY route access
     * Returns 200 ONLY if admin is currently authenticated and session is valid
     * Returns 401 if not authenticated, session expired, or token revoked
     */
    public function me(Request $request)
    {
        // Debug logging in development
        if (config('app.env') !== 'production') {
            Log::debug('AdminController::me called', [
                'has_auth_header' => $request->hasHeader('Authorization'),
                'auth_header_preview' => $request->header('Authorization') ? substr($request->header('Authorization'), 0, 30) . '...' : 'missing',
                'has_cookie' => $request->hasCookie('auth_token'),
                'cookie_value_preview' => $request->cookie('auth_token') ? substr($request->cookie('auth_token'), 0, 30) . '...' : 'missing',
                'bearer_token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 30) . '...' : 'missing',
            ]);
        }

        $admin = $request->user();

        // NO ADMIN = NOT LOGGED IN (401)
        if (!$admin) {
            if (config('app.env') !== 'production') {
                Log::debug('AdminController::me - User not authenticated');
            }
            return response()->json([
                'logged_in' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Verify token is still valid (not revoked)
        // For Sanctum, check if current token exists and is valid
        if (method_exists($admin, 'currentAccessToken')) {
            $token = $admin->currentAccessToken();
            if (!$token) {
                // Token was revoked or doesn't exist
                return response()->json([
                    'logged_in' => false,
                    'message' => 'Session expired or revoked.'
                ], 401);
            }
        }

        // ADMIN EXISTS + TOKEN VALID = LOGGED IN (200)
        return response()->json([
            'logged_in' => true,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'avatar' => $admin->avatar ?? null,
            ],
        ], 200);
    }

    /**
     * Get authenticated admin's profile.
     */
    public function profile(Request $request)
    {

        $admin = $request->user();

        if (!$admin) {
            Log::error('Admin profile: User not authenticated');
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'data' => $admin,
        ]);
    }

    /**
     * Update authenticated admin's profile (name, email, password, avatar).
     * Returns a fresh personal access token so the frontend can re-authenticate
     * after changing credentials.
     */
    public function update(Request $request)
    {
        $admin = $request->user();

        Log::debug('AdminController::update called', [
            'authenticated_user' => $admin ? get_class($admin) . ' #' . $admin->id : 'null',
            'auth_user' => auth()->user() ? get_class(auth()->user()) : 'null',
            'auth_sanctum' => auth('sanctum')->user() ? get_class(auth('sanctum')->user()) : 'null',
        ]);

        if (! $admin) {
            Log::error('Admin update: User not authenticated', [
                'request_headers' => $request->headers->all(),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            // password is optional; if provided, minimum length enforced
            'password' => 'nullable|string|min:6',
            // avatar is optional and should be a valid URL to avoid invalid data
            'avatar' => 'nullable|url|max:2048'
        ]);

        $admin->name = $request->input('name', $admin->name);
        $admin->email = $request->input('email', $admin->email);

        if ($request->filled('avatar')) {
            $admin->avatar = $request->input('avatar');
        }

        $passwordChanged = false;
        if ($request->filled('password')) {
            $admin->password = bcrypt($request->input('password'));
            $passwordChanged = true;
        }

        try {
            $admin->save();

            // If password changed, revoke all existing tokens for security
            if ($passwordChanged && method_exists($admin, 'tokens')) {
                // tokens() is provided by Laravel\Sanctum\HasApiTokens
                $admin->tokens()->delete();
            }

            // Issue a fresh token for the frontend to use (so it can "login"
            // using the updated credentials without forcing an additional call).
            $token = $admin->createToken('api-token')->plainTextToken;

            // Persist token for debugging/inspection (optional)
            try {
                $admin->api_token = $token;
                $admin->save();
            } catch (\Exception $e) {
                // ignore failures to persist token
                Log::warning('Failed to persist admin api_token: ' . $e->getMessage());
            }

            return response()->json([
                'status' => true,
                'message' => 'Admin profile updated successfully.',
                'data' => $admin,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            // Log exception with context so frontend can return a helpful message
            Log::error('Admin update failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => $admin->id ?? null,
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Server error while updating profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
