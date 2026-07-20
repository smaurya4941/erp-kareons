<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class AttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $mrUser;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        Role::create(['name' => 'MR']);
        Role::create(['name' => 'Admin']);

        $this->mrUser = User::factory()->create([
            'status' => 'Active'
        ]);
        $this->mrUser->assignRole('MR');
        
        Storage::fake('public');
    }

    public function test_mr_cannot_check_in_with_wrong_gps_coordinates()
    {
        $response = $this->actingAs($this->mrUser)->postJson('/api/v1/mr/attendance/check-in', [
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
            'lat' => 95.000, // Invalid latitude (max 90)
            'lng' => 200.000, // Invalid longitude (max 180)
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['lat', 'lng']);
    }

    public function test_mr_can_check_in_successfully()
    {
        $response = $this->actingAs($this->mrUser)->postJson('/api/v1/mr/attendance/check-in', [
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
            'lat' => 23.0225,
            'lng' => 72.5714,
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true);
                 
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->mrUser->id
        ]);
    }

    public function test_mr_cannot_duplicate_attendance_check_in()
    {
        // First Check-in
        $this->actingAs($this->mrUser)->postJson('/api/v1/mr/attendance/check-in', [
            'selfie' => UploadedFile::fake()->image('selfie1.jpg'),
            'lat' => 23.0225,
            'lng' => 72.5714,
        ]);

        // Second Check-in
        $response = $this->actingAs($this->mrUser)->postJson('/api/v1/mr/attendance/check-in', [
            'selfie' => UploadedFile::fake()->image('selfie2.jpg'),
            'lat' => 23.0225,
            'lng' => 72.5714,
        ]);

        $response->assertStatus(400)
                 ->assertJsonPath('message', 'You have already checked in today.');
    }

    public function test_mr_cannot_check_out_twice()
    {
        // Setup initial attendance
        $this->actingAs($this->mrUser)->postJson('/api/v1/mr/attendance/check-in', [
            'selfie' => UploadedFile::fake()->image('selfie1.jpg'),
        ]);

        // First Check-out
        $this->actingAs($this->mrUser)->postJson('/api/v1/mr/attendance/check-out', [
            'selfie' => UploadedFile::fake()->image('checkout1.jpg'),
        ]);

        // Second Check-out
        $response = $this->actingAs($this->mrUser)->postJson('/api/v1/mr/attendance/check-out', [
            'selfie' => UploadedFile::fake()->image('checkout2.jpg'),
        ]);

        $response->assertStatus(400)
                 ->assertJsonPath('message', 'You have already checked out today.');
    }

    public function test_mr_cannot_upload_large_selfie_image()
    {
        // 6MB Image (Limit is 5MB)
        $largeFile = UploadedFile::fake()->create('large_selfie.jpg', 6000, 'image/jpeg');

        $response = $this->actingAs($this->mrUser)->postJson('/api/v1/mr/attendance/check-in', [
            'selfie' => $largeFile,
            'lat' => 23.0225,
            'lng' => 72.5714,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['selfie']);
    }
}
