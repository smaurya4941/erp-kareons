<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
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

    public function test_admin_dashboard_renders_with_charts(): void
    {
        $mr = User::factory()->create(['status' => 'Active']);
        $mr->assignRole('MR');

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Field Activity Trend')
            ->assertSee('Attendance Trend')
            ->assertSee('activityTrendChart');
    }

    public function test_chart_data_is_present_and_well_formed(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('chart_data', function ($data) {
                return isset($data['labels'], $data['visits'], $data['orders'], $data['samples'], $data['present'])
                    && count($data['labels']) >= 7
                    && count($data['labels']) === count($data['visits'])
                    && count($data['labels']) === count($data['present']);
            });
    }
}
