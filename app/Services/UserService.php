<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserService extends BaseService
{
    /**
     * Create a new Medical Representative or Admin.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        // Automatically generate Employee Code (e.g. MR0001) if not provided
        if (empty($data['employee_code'])) {
            $data['employee_code'] = $this->generateEmployeeCode($data['role'] ?? 'MR');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Handle photo upload
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $path = $data['photo']->store('profile-photos', 'public');
            $data['photo'] = $path;
        }

        $user = User::create($data);

        // Assign Role
        $roleName = $data['role'] ?? 'MR';
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->assignRole($role);
        }

        // Audit Log Placeholder
        // Log::info('Admin created MR', ['user_id' => $user->id]);

        return $user;
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        // Don't update employee code after creation
        unset($data['employee_code']);

        // Handle photo upload
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $data['photo']->store('profile-photos', 'public');
            $data['photo'] = $path;
        }

        $user->update($data);

        // Update Role if provided
        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        // Audit Log Placeholder
        // Log::info('Admin updated MR', ['user_id' => $user->id]);

        return $user;
    }

    /**
     * Toggle the active status of a user.
     *
     * @param User $user
     * @return User
     */
    public function toggleStatus(User $user): User
    {
        $user->update(['status' => !$user->status]);
        
        // Audit Log Placeholder
        // Log::info('Admin toggled MR status', ['user_id' => $user->id, 'new_status' => $user->status]);

        return $user;
    }

    /**
     * Soft delete a user.
     *
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): bool
    {
        // Audit Log Placeholder
        // Log::info('Admin soft deleted MR', ['user_id' => $user->id]);

        return $user->delete();
    }

    /**
     * Change user password.
     *
     * @param User $user
     * @param string $newPassword
     * @return User
     */
    public function changePassword(User $user, string $newPassword): User
    {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
        
        // Audit Log Placeholder
        // Log::info('Password changed', ['user_id' => $user->id]);

        return $user;
    }

    /**
     * Generate a unique Employee Code based on Role.
     *
     * @param string $role
     * @return string
     */
    private function generateEmployeeCode(string $role): string
    {
        $prefix = strtoupper(substr($role, 0, 2)); // AD for Admin, MR for MR
        
        // Find the last user with this prefix
        $lastUser = User::where('employee_code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastUser) {
            return $prefix . '0001';
        }

        // Extract the number part
        $lastNumber = (int) substr($lastUser->employee_code, strlen($prefix));
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
