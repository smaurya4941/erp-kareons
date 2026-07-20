<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->getRedirectRoute(Auth::user()));
        }
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $data = $this->authService->login($request->validated(), $request->boolean('remember'), 'web');
            $request->session()->regenerate();
            
            return redirect($data['redirect']);
        } catch (ValidationException $e) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->onlyInput('email');
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $this->authService->logout(Auth::user(), 'web');
        }
        return redirect()->route('login');
    }

    private function getRedirectRoute($user)
    {
        return $user->hasRole('Admin') ? route('admin.dashboard') : route('mr.dashboard');
    }
}
