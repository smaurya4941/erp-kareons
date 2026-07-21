<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'MR']);
        Role::create(['name' => 'Admin']);
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

    public function test_profile_page_renders(): void
    {
        $this->actingAs($this->mr())
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Update Password');
    }

    public function test_user_can_update_profile_fields(): void
    {
        $user = $this->mr();

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'phone' => '9876543210',
            'gender' => 'Male',
            'dob' => '1990-05-20',
            'address' => 'New Address 123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $user->fresh();
        $this->assertSame('Updated Name', $fresh->name);
        $this->assertSame('9876543210', $fresh->phone);
        $this->assertSame('Male', $fresh->gender);
        $this->assertSame('1990-05-20', $fresh->dob->format('Y-m-d'));
        $this->assertSame('New Address 123', $fresh->address);
    }

    public function test_user_can_update_password_with_correct_current_password(): void
    {
        $user = $this->mr();

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'OldPass123',
            'password' => 'BrandNew123',
            'password_confirmation' => 'BrandNew123',
        ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('BrandNew123', $user->fresh()->password));
    }

    public function test_password_update_rejects_wrong_current_password(): void
    {
        $user = $this->mr();

        $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'WrongPass000',
            'password' => 'BrandNew123',
            'password_confirmation' => 'BrandNew123',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('OldPass123', $user->fresh()->password));
    }
}
