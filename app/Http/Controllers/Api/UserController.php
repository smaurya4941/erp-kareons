<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Helpers\ActivityLogger;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users (MRs).
     */
    public function index(Request $request): JsonResponse
    {
        // For API, we might want pagination and filters.
        $query = User::role('MR');
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->get('per_page', 15));

        return $this->successResponse(
            UserResource::collection($users)->response()->getData(true),
            'Users retrieved successfully'
        );
    }

    /**
     * Store a newly created MR.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());
        return $this->successResponse(new UserResource($user), 'User created successfully', 201);
    }

    /**
     * Display the specified MR.
     */
    public function show(User $user): JsonResponse
    {
        return $this->successResponse(new UserResource($user), 'User retrieved successfully');
    }

    /**
     * Update the specified MR.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->updateUser($user, $request->validated());
        return $this->successResponse(new UserResource($user), 'User updated successfully');
    }

    /**
     * Remove the specified MR (Soft delete).
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->deleteUser($user);
        return $this->successResponse([], 'User deleted successfully');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(User $user): JsonResponse
    {
        $user = $this->userService->toggleStatus($user);
        return $this->successResponse(new UserResource($user), 'User status toggled successfully');
    }

    /**
     * Admin resets an MR's password (no current-password check required).
     */
    public function resetPassword(ResetPasswordRequest $request, User $user): JsonResponse
    {
        $this->userService->changePassword($user, $request->validated()['password']);

        ActivityLogger::log(
            'User Management',
            'Reset Password',
            "Admin reset the password for {$user->name} ({$user->employee_code}).",
            $user,
            null,
            'Success',
            'Warning'
        );

        return $this->successResponse([], "Password for {$user->name} has been reset successfully.");
    }

    /**
     * Change Password.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $request->user()->password)) {
            return $this->errorResponse('Current password does not match.', 400);
        }

        $this->userService->changePassword($request->user(), $request->password);
        return $this->successResponse([], 'Password changed successfully');
    }
}
