<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        Log::debug('Login request', [
            'request' => $request->all(),
        ]);
        // 1. Validate request
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Rate limiting
        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => ['Too many login attempts. Please try again later.'],
            ]);
        }

        // 3. Fetch admin
        $admin = \App\Models\Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        // 4. Create Sanctum token for API authentication
        // $token = $admin->createToken('api-token', ['*'])->plainTextToken;

        // 5. Log the admin in using Laravel Auth (SESSION) for web routes
        Auth::guard('web')->login($admin);

        // 6. Regenerate session ONCE (security)
        $request->session()->regenerate();

        // 7. Return user data with token set in HTTP-only cookie
        // For cross-origin cookies to work, we need to set domain explicitly
        // In development, use null domain and sameSite: 'lax'
        // In production, use sameSite: 'none' and secure: true
        // $isProduction = config('app.env') === 'production';
        // $cookie = cookie(
        //     'auth_token',
        //     $token,
        //     60 * 24 * 7, // 7 days
        //     '/', // path
        //     null, // domain (null = current domain, works for same-origin)
        //     $isProduction, // secure: true in production only (required for sameSite: 'none')
        //     true, // httpOnly
        //     false, // raw
        //     $isProduction ? 'none' : 'lax' // sameSite: 'none' for cross-origin in production, 'lax' for dev
        // );

        // Debug logging
        // if (config('app.env') !== 'production') {
        //     Log::debug('Login cookie set', [
        //         'cookie_name' => 'auth_token',
        //         'token_length' => strlen($token),
        //         'domain' => null,
        //         'path' => '/',
        //         'secure' => $isProduction,
        //         'httpOnly' => true,
        //         'sameSite' => $isProduction ? 'none' : 'lax',
        //     ]);
        // }

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'avatar' => $admin->avatar ?? null,
            ]
        ], 200);
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
