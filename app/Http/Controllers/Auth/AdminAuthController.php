<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        // 1️⃣ Validate request
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // 2️⃣ Authenticate using ADMIN guard (IMPORTANT)
        if (!Auth::guard('admin')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // 3️⃣ Regenerate session (prevents session fixation)
        $request->session()->regenerate();

        $admin = Auth::guard('admin')->user();

        // 4️⃣ Return safe user payload
        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id'     => $admin->id,
                'name'   => $admin->name,
                'email'  => $admin->email,
                'avatar' => $admin->avatar ?? null,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out'
        ], 200);
    }
}
