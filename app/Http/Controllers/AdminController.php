<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
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
