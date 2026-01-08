<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\EmployeeProfile;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_calculation_is_accurate()
    {
        // Setup Tax Bands
        $this->seed(\Database\Seeders\TaxBandSeeder::class);

        // Create Employee
        $user = User::factory()->create();
        EmployeeProfile::create([
            'user_id' => $user->id,
            'job_title' => 'Test Staff',
            'basic_salary' => 5000.00,
            'employment_status' => 'Full-time'
        ]);

        // Run Calculation
        $service = new PayrollService();
        $service->generatePayrollForMonth('2026-01');

        $payroll = \App\Models\Payroll::where('user_id', $user->id)->first();

        $this->assertNotNull($payroll);
        $this->assertEquals(5000, $payroll->gross_salary);

        // Expected Logic (Manual Calc)
        // Basic: 5000
        // Tier 2 (5.5%): 275
        // Taxable: 4725

        // PAYE:
        // 0-490 (490) * 0% = 0
        // 490-600 (110) * 5% = 5.5
        // 600-730 (130) * 10% = 13.0
        // 730-3730 (3000) * 17.5% = 525.0
        // 3730- (Rem: 4725 - 3730 = 995) * 25% = 248.75
        // Total PAYE = 5.5 + 13 + 525 + 248.75 = 792.25

        // Net = 5000 - 275 - 792.25 = 3932.75

        $this->assertEquals(275.00, $payroll->tier_2);
        $this->assertEquals(792.25, $payroll->paye_tax);
        $this->assertEquals(3932.75, $payroll->net_salary);
    }
}
