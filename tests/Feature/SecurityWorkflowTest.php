<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class SecurityWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'MR']);
        Role::create(['name' => 'Admin']);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes()
    {
        // Accessing without Sanctum token
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(401)
                 ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_mr_cannot_access_admin_endpoints()
    {
        $mrUser = User::factory()->create(['status' => 'Active']);
        $mrUser->assignRole('MR');

        // Accessing Admin users list
        $response = $this->actingAs($mrUser)->getJson('/api/v1/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_endpoints()
    {
        $adminUser = User::factory()->create(['status' => 'Active']);
        $adminUser->assignRole('Admin');

        $response = $this->actingAs($adminUser)->getJson('/api/v1/users');

        $response->assertStatus(200);
    }
}
