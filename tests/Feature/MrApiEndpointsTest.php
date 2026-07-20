<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\DoctorVisit;
use App\Models\Product;
use App\Models\SampleAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MrApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected User $mr;
    protected User $admin;
    protected Product $product;
    protected DoctorVisit $visit;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'MR']);
        Role::create(['name' => 'Admin']);

        $this->mr = User::factory()->create(['status' => 'Active']);
        $this->mr->assignRole('MR');

        $this->admin = User::factory()->create(['status' => 'Active']);
        $this->admin->assignRole('Admin');

        $this->product = Product::create([
            'product_code' => 'P001', 'name' => 'Test Medicine', 'brand' => 'KareOns',
            'category' => 'Tablet', 'strength' => '500mg', 'pack_size' => '10x10',
            'price' => 100, 'status' => true,
        ]);

        SampleAssignment::create([
            'user_id' => $this->mr->id, 'product_id' => $this->product->id,
            'assigned_quantity' => 20, 'distributed_quantity' => 0,
        ]);

        Attendance::create([
            'user_id' => $this->mr->id, 'date' => Carbon::today()->toDateString(),
            'check_in_time' => now(), 'check_in_selfie' => 'fake/path.jpg', 'status' => 'Incomplete',
        ]);

        $this->visit = DoctorVisit::create([
            'user_id' => $this->mr->id, 'date' => Carbon::today()->toDateString(),
            'time' => now()->format('H:i:s'), 'doctor_name' => 'Dr. Smith',
            'specialization' => 'Cardiology', 'discussion_summary' => 'x',
            'doctor_response' => 'Positive', 'status' => 'Completed',
        ]);
    }

    public function test_mr_can_add_product_discussion_to_visit()
    {
        $response = $this->actingAs($this->mr)->postJson('/api/v1/mr/product-discussions', [
            'doctor_visit_id' => $this->visit->id,
            'products' => [
                ['product_id' => $this->product->id, 'interest_level' => 'High'],
            ],
        ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
        $this->assertDatabaseHas('doctor_visit_products', [
            'doctor_visit_id' => $this->visit->id, 'product_id' => $this->product->id,
        ]);
    }

    public function test_mr_can_distribute_samples_via_standalone_endpoint()
    {
        $response = $this->actingAs($this->mr)->postJson('/api/v1/mr/sample-distributions', [
            'doctor_visit_id' => $this->visit->id,
            'samples' => [['product_id' => $this->product->id, 'quantity' => 5]],
        ]);

        $response->assertStatus(201)->assertJsonPath('success', true);

        $assignment = SampleAssignment::where('user_id', $this->mr->id)->first();
        $this->assertEquals(15, $assignment->assigned_quantity);
    }

    public function test_mr_cannot_distribute_more_than_remaining()
    {
        $response = $this->actingAs($this->mr)->postJson('/api/v1/mr/sample-distributions', [
            'doctor_visit_id' => $this->visit->id,
            'samples' => [['product_id' => $this->product->id, 'quantity' => 999]],
        ]);

        $response->assertStatus(400)->assertJsonPath('success', false);
    }

    public function test_mr_cannot_touch_another_mrs_visit()
    {
        $otherVisit = DoctorVisit::create([
            'user_id' => $this->admin->id, 'date' => Carbon::today()->toDateString(),
            'time' => now()->format('H:i:s'), 'doctor_name' => 'Dr. X',
            'specialization' => 'ENT', 'discussion_summary' => 'x',
            'doctor_response' => 'Neutral', 'status' => 'Completed',
        ]);

        $this->actingAs($this->mr)->postJson('/api/v1/mr/product-discussions', [
            'doctor_visit_id' => $otherVisit->id,
            'products' => [['product_id' => $this->product->id, 'interest_level' => 'High']],
        ])->assertStatus(400);
    }

    public function test_mr_can_record_and_list_order()
    {
        $create = $this->actingAs($this->mr)->postJson('/api/v1/mr/orders', [
            'doctor_visit_id' => $this->visit->id,
            'items' => [['product_id' => $this->product->id, 'quantity' => 3]],
        ]);

        $create->assertStatus(201)->assertJsonPath('data.status', 'Pending');
        $this->assertDatabaseHas('orders', ['user_id' => $this->mr->id, 'status' => 'Pending']);

        $this->actingAs($this->mr)->getJson('/api/v1/mr/orders')
            ->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_admin_can_list_orders_without_relationship_error()
    {
        $this->actingAs($this->mr)->postJson('/api/v1/mr/orders', [
            'doctor_visit_id' => $this->visit->id,
            'items' => [['product_id' => $this->product->id, 'quantity' => 2]],
        ])->assertStatus(201);

        // Previously crashed with RelationNotFoundException on `doctor`.
        $this->actingAs($this->admin)->getJson('/api/v1/orders')
            ->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_admin_reports_endpoints_do_not_crash()
    {
        // 'visits', 'orders', 'samples' previously referenced a bad `doctor`
        // relation / non-existent SampleDistribution model.
        foreach (['attendance', 'visits', 'orders', 'samples'] as $type) {
            $this->actingAs($this->admin)->getJson("/api/v1/reports/{$type}")
                ->assertStatus(200)->assertJsonPath('success', true);
        }
    }

    public function test_admin_daily_report_summary_uses_correct_columns()
    {
        // Previously referenced non-existent `report_date` / `total_visits` columns.
        $this->actingAs($this->admin)->getJson('/api/v1/daily-report/summary')
            ->assertStatus(200)->assertJsonPath('success', true);
    }
}
