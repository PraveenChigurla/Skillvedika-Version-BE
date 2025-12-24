<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 403);
        }

        // Log the admin into the session (SPA cookie-based auth via Sanctum)
        Auth::login($admin);
        // regenerate session id to prevent fixation
        $request->session()->regenerate();

        // Delete previous tokens (optional but recommended for security)
        $admin->tokens()->delete();

        // Create a fresh personal access token (still useful for API debugging)
        $token = $admin->createToken('admin_token')->plainTextToken;

        // Persist token in admins table for debugging/inspection (don't fail login on DB write)
        try {
            $admin->api_token = $token;
            $admin->save();
        } catch (\Exception $e) {
            // swallow - login/session still succeeds
        }

        return response()->json([
            'token' => $token,
            'user'  => $admin
        ], 200);
    }

    /**
     * Logout the authenticated admin (invalidate session)
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate and regenerate session token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out'], 200);
    }
}
