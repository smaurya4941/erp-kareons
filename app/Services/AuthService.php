<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthService extends BaseService
{
    /**
     * Authenticate a user and create session/token.
     *
     * @param array $credentials
     * @param bool $remember
     * @param string $device
     * @return array
     * @throws ValidationException
     */
    public function login(array $credentials, bool $remember = false, string $device = 'web'): array
    {
        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->status) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Your account has been deactivated. Please contact the administrator.'),
            ]);
        }

        $token = null;
        
        // If it's not a web login, generate API token
        if ($device !== 'web') {
            // Delete old tokens for the device if you want single device login, or keep them
            $token = $user->createToken($device)->plainTextToken;
        }

        // Basic Audit Logging (Placeholder for Phase 13)
        // Log::info('User logged in', ['user_id' => $user->id]);

        return [
            'user' => $user,
            'token' => $token,
            'role' => $user->roles->first()?->name ?? 'MR',
            'redirect' => $user->hasRole('Admin') ? route('admin.dashboard') : route('mr.dashboard')
        ];
    }

    /**
     * Logout the user.
     *
     * @param User $user
     * @param string $device
     * @return void
     */
    public function logout(User $user, string $device = 'web'): void
    {
        if ($device !== 'web') {
            // Revoke current token
            $user->currentAccessToken()->delete();
        } else {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        // Basic Audit Logging (Placeholder for Phase 13)
        // Log::info('User logged out', ['user_id' => $user->id]);
    }
}
