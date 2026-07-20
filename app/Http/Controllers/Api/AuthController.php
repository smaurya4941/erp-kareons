<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle API login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $device = $request->header('User-Agent', 'mobile');
            $data = $this->authService->login($request->validated(), false, $device);

            return $this->successResponse([
                'token' => $data['token'],
                'user' => new \App\Http\Resources\UserResource($data['user']),
                'role' => $data['role']
            ], 'Login successful');

        } catch (ValidationException $e) {
            return $this->errorResponse('Invalid email or password.', 401);
        }
    }

    /**
     * Handle API logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user(), 'mobile');
        
        return $this->successResponse([], 'Logged out successfully');
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        return $this->successResponse(new \App\Http\Resources\UserResource($request->user()), 'User profile retrieved successfully');
    }
}
