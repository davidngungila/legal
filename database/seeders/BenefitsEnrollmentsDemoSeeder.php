<?php

namespace Database\Seeders;

use App\Models\BenefitEnrollment;
use App\Models\BenefitPlan;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class BenefitsEnrollmentsDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $client = Client::orderBy('id')->first();
        if (!$client) {
            return;
        }

        if (BenefitEnrollment::withoutClientFilter()->where('client_id', $client->id)->exists()) {
            return;
        }

        $employees = Employee::where('client_id', $client->id)->where('status', 'active')->get();
        if ($employees->isEmpty()) {
            return;
        }

        $plans = BenefitPlan::withoutClientFilter()
            ->where('client_id', $client->id)
            ->get()
            ->keyBy('name');

        $rows = [];

        foreach ($employees as $employee) {
            $benefits = is_array($employee->benefits) ? $employee->benefits : [];

            if (isset($plans['NSSF Contribution'])) {
                $rows[] = ['plan' => $plans['NSSF Contribution'], 'employee' => $employee];
            }

            foreach (['Health Insurance - Comprehensive' => 'Health Insurance', 'Transport Allowance' => 'Transport Allowance', 'Phone / Internet' => 'Phone / Internet'] as $planName => $benefitKey) {
                if (isset($plans[$planName]) && in_array($benefitKey, $benefits)) {
                    $rows[] = ['plan' => $plans[$planName], 'employee' => $employee];
                }
            }
        }

        foreach ($rows as $row) {
            BenefitEnrollment::create([
                'client_id' => $client->id,
                'employee_id' => $row['employee']->id,
                'plan_id' => $row['plan']->id,
                'effective_date' => now()->toDateString(),
                'employee_cost' => 0,
                'employer_cost' => 0,
                'status' => 'enrolled',
            ]);
        }
    }
}
