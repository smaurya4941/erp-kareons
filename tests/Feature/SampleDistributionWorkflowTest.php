<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\SampleAssignment;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class SampleDistributionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $mrUser;
    protected Product $product;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'MR']);
        $this->mrUser = User::factory()->create(['status' => 'Active']);
        $this->mrUser->assignRole('MR');

        $this->product = Product::create([
            'product_code' => 'P001',
            'name' => 'Test Medicine',
            'brand' => 'KareOns',
            'category' => 'Tablet',
            'strength' => '500mg',
            'pack_size' => '10x10',
            'price' => 100,
            'status' => true
        ]);

        // Assign 20 samples to MR
        SampleAssignment::create([
            'user_id' => $this->mrUser->id,
            'product_id' => $this->product->id,
            'assigned_quantity' => 20,
            'distributed_quantity' => 0
        ]);

        // Need attendance to create a visit
        Attendance::create([
            'user_id' => $this->mrUser->id,
            'date' => Carbon::today()->toDateString(),
            'check_in_time' => now(),
            'check_in_selfie' => 'fake/path.jpg',
            'status' => 'Incomplete'
        ]);
    }

    public function test_mr_cannot_over_distribute_samples()
    {
        // Try to distribute 50 samples during a visit (only 20 assigned)
        $response = $this->actingAs($this->mrUser)->postJson('/api/v1/mr/visits', [
            'doctor_name' => 'Dr. Smith',
            'specialization' => 'Cardiology',
            'discussion_summary' => 'Discussed heart health',
            'doctor_response' => 'Positive',
            'products' => [
                [
                    'product_id' => $this->product->id,
                    'interest_level' => 'High'
                ]
            ],
            'samples' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 50 // OVER DISTRIBUTION
                ]
            ]
        ]);

        // Should return 400 with the exception message from SampleAssignmentService
        $response->assertStatus(400)
                 ->assertJsonPath('success', false)
                 ->assertSee('Cannot reduce by 50');
    }

    public function test_mr_can_distribute_valid_samples()
    {
        $response = $this->actingAs($this->mrUser)->postJson('/api/v1/mr/visits', [
            'doctor_name' => 'Dr. Smith',
            'specialization' => 'Cardiology',
            'discussion_summary' => 'Discussed heart health',
            'doctor_response' => 'Positive',
            'products' => [
                [
                    'product_id' => $this->product->id,
                    'interest_level' => 'High'
                ]
            ],
            'samples' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 15 // VALID
                ]
            ]
        ]);

        $response->assertStatus(201);
        
        $assignment = SampleAssignment::where('user_id', $this->mrUser->id)
                                      ->where('product_id', $this->product->id)
                                      ->first();

        // Initially 20 assigned. We distributed 15. The 'assigned_quantity' is reduced by adjustSamples() logic.
        $this->assertEquals(5, $assignment->assigned_quantity);
    }
}
