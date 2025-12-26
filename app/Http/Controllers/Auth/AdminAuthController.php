<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
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

        // Set cookie (not encrypted because auth_token is in EncryptCookies $except array)
        // This allows the token to be extracted and used as Bearer token if needed
        // The cookie is still HTTP-only for security
        $cookie = cookie(
            'auth_token',
            $token,
            60 * 24 * 30, // minutes (30 days)
            '/',
            null, // domain (null = current domain)
            $isSecure,  // secure (HTTPS only in production, false for localhost)
            true,  // httpOnly
            false, // raw (false is fine since we excluded it from encryption)
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


        $user = null;
        $token = null;

        // Try to authenticate if Bearer token is present
        // This allows logout to work even without auth:sanctum middleware
        if ($request->bearerToken()) {
            $tokenValue = $request->bearerToken();
            // Find the token in the database
            $token = PersonalAccessToken::findToken($tokenValue);
            if ($token) {
                $user = $token->tokenable;
            }
        }

        // Debug: Log authentication details in development
        if (config('app.env') !== 'production') {
            Log::debug('Logout request', [
                'has_auth_header' => $request->hasHeader('Authorization'),
                'auth_header' => $request->header('Authorization') ? 'present' : 'missing',
                'has_cookie' => $request->hasCookie('auth_token'),
                'user_authenticated' => $user ? 'yes' : 'no',
                'user_id' => $user?->id,
            ]);
        }

        if ($user && $token) {
            // Delete the specific token used for this request
            $token->delete();

            // Optionally revoke all tokens for this user
            // Uncomment if you want to log out from all devices
            // if (method_exists($user, 'tokens')) {
            //     $user->tokens()->delete();
            // }
        }


        // Clear the auth_token cookie

        return response()
            ->json([
                'message' => 'Logged out successfully'
            ])
            ->withCookie(
                cookie()->forget('auth_token')
            );
    }
}
