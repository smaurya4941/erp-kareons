<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class DoctorVisitWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $mrUser;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'MR']);
        $this->mrUser = User::factory()->create(['status' => 'Active']);
        $this->mrUser->assignRole('MR');

        Attendance::create([
            'user_id' => $this->mrUser->id,
            'date' => Carbon::today()->toDateString(),
            'check_in_time' => now(),
            'check_in_selfie' => 'fake/path.jpg',
            'status' => 'Incomplete'
        ]);
    }

    public function test_mr_cannot_submit_visit_without_doctor_details()
    {
        // Submitting without 'doctor_name' and 'specialization'
        $response = $this->actingAs($this->mrUser)->postJson('/api/v1/mr/visits', [
            // Missing doctor details
            'discussion_summary' => 'Discussed heart health',
            'doctor_response' => 'Positive',
            'products' => [
                [
                    'product_id' => 1,
                    'interest_level' => 'High'
                ]
            ]
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['doctor_name', 'specialization']);
    }

    public function test_mr_cannot_submit_visit_without_products()
    {
        $response = $this->actingAs($this->mrUser)->postJson('/api/v1/mr/visits', [
            'doctor_name' => 'Dr. Smith',
            'specialization' => 'Cardiology',
            'discussion_summary' => 'Discussed heart health',
            'doctor_response' => 'Positive',
            // Missing 'products' array which is required by business logic
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['products']);
    }
}
