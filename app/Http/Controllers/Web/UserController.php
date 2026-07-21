<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Helpers\ActivityLogger;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $query = User::role('MR');
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $users = $query->paginate($request->get('per_page', 10))->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->createUser($request->validated());
        return redirect()->route('admin.users.index')->with('success', 'MR created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser($user, $request->validated());
        return redirect()->route('admin.users.index')->with('success', 'MR updated successfully.');
    }

    public function resetPassword(ResetPasswordRequest $request, User $user)
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

        return back()->with('success', "Password for {$user->name} has been reset successfully.");
    }

    public function toggleStatus(User $user)
    {
        $this->userService->toggleStatus($user);
        return back()->with('success', 'User status updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);
        return back()->with('success', 'User deleted successfully.');
    }
}
