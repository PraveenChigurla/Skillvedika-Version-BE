<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validate request (auto throws 422 on failure)
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Rate limiting (anti brute-force)
        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => ['Too many login attempts. Please try again later.'],
            ]);
        }

        // 3. Attempt authentication using Admin model
        $admin = \App\Models\Admin::where('email', $credentials['email'])->first();

        if (!$admin || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $admin->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        // 4. Authenticated user
        $user = $admin;

        // 5. (Optional) Revoke old tokens
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        // 6. Create token (name = device/browser)
        $token = $user->createToken(
            $request->userAgent() ?? 'api-token'
        )->plainTextToken;


        // 7. Secure HTTP-only cookie
        // Set secure flag based on environment (true for HTTPS, false for localhost)
        $isSecure = config('app.env') === 'production' || $request->secure();

        // print_r($isSecure);
        // die();
        $cookie = cookie(
            'auth_token',
            $token,
            60 * 24 * 30, // minutes (30 days)
            '/',
            null, // domain (null = current domain)
            true,  // secure (HTTPS only in production)
            true,  // httpOnly
            false, // raw
            'Lax'  // sameSite
        );

        // 8. Return sanitized response
        return response()
            ->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? null,
                ],
            ], 200)
            ->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // Revoke current token (recommended)
            if (method_exists($user, 'currentAccessToken')) {
                $request->user()->currentAccessToken()?->delete();
            }

            // OR revoke all tokens (optional)
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }
        }

        return response()
            ->json([
                'message' => 'Logged out successfully'
            ])
            ->withCookie(
                cookie()->forget('auth_token')
            );
    }
}
