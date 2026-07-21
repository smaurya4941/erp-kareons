<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'MR']);
        Role::create(['name' => 'Admin']);
    }

    private function admin(): User
    {
        $admin = User::factory()->create(['status' => 'Active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    private function mr(): User
    {
        $mr = User::factory()->create([
            'status' => 'Active',
            'password' => Hash::make('OldPass123'),
        ]);
        $mr->assignRole('MR');

        return $mr;
    }

    public function test_admin_can_reset_mr_password(): void
    {
        $mr = $this->mr();

        $response = $this->actingAs($this->admin())
            ->post(route('admin.users.reset-password', $mr), [
                'password' => 'NewPass123',
                'password_confirmation' => 'NewPass123',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('NewPass123', $mr->fresh()->password));

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'User Management',
            'action' => 'Reset Password',
            'subject_id' => $mr->id,
        ]);
    }

    public function test_password_reset_requires_confirmation_and_strength(): void
    {
        $mr = $this->mr();

        $this->actingAs($this->admin())
            ->post(route('admin.users.reset-password', $mr), [
                'password' => 'short',
                'password_confirmation' => 'mismatch',
            ])
            ->assertSessionHasErrors('password');

        // Password must remain unchanged after a failed attempt.
        $this->assertTrue(Hash::check('OldPass123', $mr->fresh()->password));
    }

    public function test_mr_cannot_reset_another_users_password(): void
    {
        $target = $this->mr();
        $attacker = $this->mr();

        $this->actingAs($attacker)
            ->post(route('admin.users.reset-password', $target), [
                'password' => 'NewPass123',
                'password_confirmation' => 'NewPass123',
            ])
            ->assertStatus(403);

        $this->assertTrue(Hash::check('OldPass123', $target->fresh()->password));
    }
}
