<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\EmployeeProfile;
use App\Models\Appraisal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrmErrorTest extends TestCase
{
    use RefreshDatabase;

    protected $branchA;
    protected $branchB;
    protected $adminA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branchA = Branch::create(['name' => 'Branch A']);
        $this->branchB = Branch::create(['name' => 'Branch B']);

        $this->adminA = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $this->branchA->id,
            'is_super_admin' => false
        ]);
    }

    public function test_non_admin_cannot_access_hr_dashboard()
    {
        $nurse = User::factory()->create(['role' => 'nurse']);

        $response = $this->actingAs($nurse)->get(route('admin.hr.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_cannot_view_employee_of_other_branch()
    {
        $empB = User::factory()->create(['branch_id' => $this->branchB->id, 'role' => 'nurse']);
        EmployeeProfile::create(['user_id' => $empB->id]);

        // Attempt to view edit page of employee B
        $response = $this->actingAs($this->adminA)
            ->get(route('admin.hr.employees.edit', $empB));

        // This should fail with 403 or 404 if scoped correctly
        // Currently expecting 200 (Fail) because I know I haven't secured it yet
        $response->assertStatus(403);
    }

    public function test_admin_cannot_update_employee_of_other_branch()
    {
        $empB = User::factory()->create(['branch_id' => $this->branchB->id, 'role' => 'nurse']);

        $response = $this->actingAs($this->adminA)
            ->put(route('admin.hr.employees.update', $empB), [
                'name' => 'Hacked Name',
                'email' => $empB->email
            ]);

        $response->assertStatus(403);
    }

    public function test_cannot_create_duplicate_appraisal()
    {
        $empA = User::factory()->create(['branch_id' => $this->branchA->id, 'role' => 'nurse']);

        // Create first appraisal
        Appraisal::create([
            'user_id' => $empA->id,
            'reviewer_id' => $this->adminA->id,
            'period_month' => '2026-01',
            'appraisal_date' => now(),
            'total_score' => 0
        ]);

        // Attempt duplicate via Controller
        $response = $this->actingAs($this->adminA)
            ->post(route('admin.hr.appraisals.store'), [
                'user_id' => $empA->id,
                'period_month' => '2026-01',
                'appraisal_date' => now()->toDateString()
            ]);

        // Should redirect to edit page with info message, NOT create new one
        $response->assertStatus(302);
        $this->assertCount(1, Appraisal::all());
    }
}
