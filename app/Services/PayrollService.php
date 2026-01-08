<?php

namespace App\Services;

use App\Models\EmployeeProfile;
use App\Models\Payroll;
use App\Models\TaxBand;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Generate Payroll for a specific month for all eligible employees.
     */
    public function generatePayrollForMonth($monthYear, $branchId = null) // "2026-01"
    {
        $employees = User::whereHas('employeeProfile', function ($q) {
            $q->where('basic_salary', '>', 0); // Only paid staff
        })
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with('employeeProfile')->get();

        $count = 0;
        foreach ($employees as $employee) {
            // Check if already exists
            if (Payroll::where('user_id', $employee->id)->where('month_year', $monthYear)->exists()) {
                continue;
            }

            $this->calculateAndCreatePayroll($employee, $monthYear);
            $count++;
        }

        return $count;
    }

    /**
     * Calculate and save single payroll record.
     */
    public function calculateAndCreatePayroll(User $employee, $monthYear)
    {
        $profile = $employee->employeeProfile;
        $basic = $profile->basic_salary;

        // 1. Allowances (Could be fetched from a 'SalaryStructure' table later, generic for now)
        $allowances = 0;

        $gross = $basic + $allowances;

        // 2. Statutory Deductions
        // Tier 1 (SSNIT - Employer): 13% of Basic. (Cost to company, not deduction from gross usually, but kept for record)
        // Tier 2 (SSNIT - Employee): 5.5% of Basic.
        // Tier 3 (Provident): Optional. Let's assume 0 for now or add field to profile.

        $tier1_employer = $basic * 0.13;
        $tier2_employee = $basic * 0.055;
        $tier3 = 0; // Configurable later

        // 3. Taxable Income
        // Taxable = Gross - (Tier 2 + Tier 3) [Reliefs]
        $taxableIncome = $gross - ($tier2_employee + $tier3);
        if ($taxableIncome < 0)
            $taxableIncome = 0;

        // 4. PAYE Calculation
        $paye = $this->calculatePaye($taxableIncome);

        // 5. Net Salary
        $net = $gross - ($tier2_employee + $tier3 + $paye);

        // 6. Save
        return Payroll::create([
            'user_id' => $employee->id,
            'month_year' => $monthYear,
            'basic_salary' => $basic,
            'total_allowances' => $allowances,
            'gross_salary' => $gross,
            'tier_1' => $tier1_employer,
            'tier_2' => $tier2_employee,
            'tier_3' => $tier3,
            'paye_tax' => $paye,
            'net_salary' => $net,
            'status' => 'Draft'
        ]);
    }

    /**
     * Calculate PAYE based on Database Tax Bands
     */
    public function calculatePaye($income)
    {
        $bands = TaxBand::orderBy('sort_order', 'asc')->get();
        $tax = 0;
        $remainingIncome = $income;

        foreach ($bands as $band) {
            if ($remainingIncome <= 0)
                break;

            $taxableAmount = 0;

            if ($band->band_width === null) {
                // Excess band (Top tier)
                $taxableAmount = $remainingIncome;
            } else {
                // Standard band
                $taxableAmount = min($remainingIncome, $band->band_width);
            }

            $tax += $taxableAmount * ($band->tax_rate / 100);
            $remainingIncome -= $taxableAmount;
        }

        return $tax;
    }
}
