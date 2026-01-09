<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\EmployeeProfile;
use App\Models\Kpi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrmFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup initial data
        $this->branch = Branch::create(['name' => 'Test Branch', 'location' => 'Accra']);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $this->branch->id,
            'is_super_admin' => false
        ]);

        // Ensure admin has employee profile if needed for payroll? No, admin runs payroll.
    }

    public function test_hr_dashboard_access()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.hr.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('HR Dashboard');
        $response->assertSee('Total Staff');
    }

    public function test_employee_list_access_and_branch_scoping()
    {
        // Create an employee in the same branch
        $localEmp = User::factory()->create([
            'role' => 'nurse',
            'branch_id' => $this->branch->id,
            'name' => 'Local Nurse'
        ]);
        EmployeeProfile::create(['user_id' => $localEmp->id, 'basic_salary' => 2000]);

        // Create an employee in another branch
        $otherBranch = Branch::create(['name' => 'Other Branch', 'location' => 'Kumasi']);
        $remoteEmp = User::factory()->create([
            'role' => 'doctor',
            'branch_id' => $otherBranch->id,
            'name' => 'Remote Doctor'
        ]);
        EmployeeProfile::create(['user_id' => $remoteEmp->id, 'basic_salary' => 5000]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.hr.employees.index'));

        $response->assertStatus(200);
        $response->assertSee('Local Nurse');
        $response->assertDontSee('Remote Doctor');
    }

    public function test_payroll_generation_scoped_to_branch()
    {
        // Local employee (should be paid)
        $localEmp = User::factory()->create([
            'role' => 'pharmacist',
            'branch_id' => $this->branch->id
        ]);
        EmployeeProfile::create(['user_id' => $localEmp->id, 'basic_salary' => 3000]);

        // Remote employee (should NOT be paid by this admin)
        $otherBranch = Branch::create(['name' => 'North Branch', 'location' => 'Tamale']);
        $remoteEmp = User::factory()->create([
            'role' => 'doctor',
            'branch_id' => $otherBranch->id
        ]);
        EmployeeProfile::create(['user_id' => $remoteEmp->id, 'basic_salary' => 5000]);


        $response = $this->actingAs($this->admin)
            ->post(route('admin.hr.payroll.store'), [
                'month' => date('Y-m') // Current month
            ]);

        $response->assertStatus(302); // Redirect back

        // Check Local was paid
        $this->assertDatabaseHas('payrolls', [
            'user_id' => $localEmp->id,
            'basic_salary' => 3000
        ]);

        // Check Remote was NOT paid
        $this->assertDatabaseMissing('payrolls', [
            'user_id' => $remoteEmp->id
        ]);
    }

    public function test_appraisal_flow()
    {
        $emp = User::factory()->create(['role' => 'nurse', 'branch_id' => $this->branch->id]);

        EmployeeProfile::create(['user_id' => $emp->id, 'basic_salary' => 2000]);
        // 1. Create KPI
        $kpi = Kpi::create(['name' => 'Punctuality']);

        // 2. Start Appraisal
        $response = $this->actingAs($this->admin)
            ->post(route('admin.hr.appraisals.store'), [
                'user_id' => $emp->id,
                'period_month' => '2026-01',
                'appraisal_date' => now()->toDateString()
            ]);

        $appraisal = \App\Models\Appraisal::first();
        $response->assertRedirect(route('admin.hr.appraisals.index'));

        // 3. Score Appraisal (1-5 Scale)
        $this->actingAs($this->admin)
            ->put(route('admin.hr.appraisals.update', $appraisal), [
                'scores' => [$kpi->id => 4], // Score of 4 (Very Good)
                'comments' => [$kpi->id => 'Good job'],
                'overall_comment' => 'Well done'
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('appraisal_details', [
            'appraisal_id' => $appraisal->id,
            'kpi_id' => $kpi->id,
            'score' => 4
        ]);

        // Check total calculation
        $appraisal->refresh();
        $this->assertEquals(4, $appraisal->total_score);

        // EXTRA: Test Weighted Calculation
        // Add another KPI with weight 2
        $heavyKpi = Kpi::create(['name' => 'Core Delivery', 'weight' => 2]);

        // Ensure first KPI has known weight (e.g. 1) BEFORE calc
        $kpi->update(['weight' => 1]);

        // Simulating manager update again with both KPIs
        $this->actingAs($this->admin)
            ->put(route('admin.hr.appraisals.update', $appraisal), [
                'scores' => [
                    $kpi->id => 5,
                    $heavyKpi->id => 3
                ],
                'comments' => [],
                'overall_comment' => 'Updated'
            ]);

        // Calculation: 
        // KPI 1 (Weight 1) * 5 = 5
        // KPI 2 (Weight 2) * 3 = 6
        // Total = 11. Total Weight = 3.
        // Result = 11 / 3 = 3.666... -> 3.67

        $appraisal->refresh();
        $this->assertEquals(3.67, round($appraisal->total_score, 2));
    }

    public function test_communication_flow()
    {
        $recipient = User::factory()->create(['role' => 'nurse']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.hr.communication.store'), [
                'recipient_type' => 'individual',
                'recipient_id' => $recipient->id,
                'subject' => 'Hello',
                'body' => 'Welcome to the team'
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('messages', [
            'subject' => 'Hello',
            'recipient_id' => $recipient->id,
            'is_read' => false
        ]);
    }
}
