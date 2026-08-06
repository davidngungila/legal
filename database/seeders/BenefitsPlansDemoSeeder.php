<?php

namespace Database\Seeders;

use App\Models\BenefitPlan;
use App\Models\Client;
use Illuminate\Database\Seeder;

class BenefitsPlansDemoSeeder extends Seeder
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

        if (BenefitPlan::withoutClientFilter()->where('client_id', $client->id)->exists()) {
            return;
        }

        $plans = [
            ['name' => 'Health Insurance - Comprehensive', 'category' => 'health', 'provider' => 'Jubilee Insurance', 'cost' => 25000, 'cost_period' => 'monthly', 'coverage' => '80% coverage', 'mandatory' => false, 'status' => 'active', 'description' => 'Medical, dental and vision coverage for employee and dependants.'],
            ['name' => 'Health Insurance - Basic', 'category' => 'health', 'provider' => 'Jubilee Insurance', 'cost' => 10000, 'cost_period' => 'monthly', 'coverage' => '60% coverage', 'mandatory' => false, 'status' => 'active', 'description' => 'Basic outpatient and inpatient medical cover.'],
            ['name' => 'NSSF Contribution', 'category' => 'retirement', 'provider' => 'NSSF', 'cost' => 0, 'cost_period' => 'none', 'coverage' => '10% employee / 10% employer', 'mandatory' => true, 'status' => 'active', 'description' => 'Statutory retirement savings contribution.'],
            ['name' => 'Voluntary Savings Plan', 'category' => 'retirement', 'provider' => '', 'cost' => 0, 'cost_period' => 'monthly', 'coverage' => 'Employee defined', 'mandatory' => false, 'status' => 'active', 'description' => 'Optional additional retirement savings deducted from salary.'],
            ['name' => 'Gym Membership', 'category' => 'wellness', 'provider' => 'Tanzania Fitness Center', 'cost' => 50000, 'cost_period' => 'yearly', 'coverage' => 'Employee only', 'mandatory' => false, 'status' => 'active', 'description' => 'Subsidized yearly gym membership.'],
            ['name' => 'Annual Health Checkup', 'category' => 'wellness', 'provider' => '', 'cost' => 0, 'cost_period' => 'none', 'coverage' => 'Employee only', 'mandatory' => false, 'status' => 'active', 'description' => 'Free annual medical checkup.'],
            ['name' => 'Transport Allowance', 'category' => 'additional', 'provider' => '', 'cost' => 150000, 'cost_period' => 'monthly', 'coverage' => 'Employee only', 'mandatory' => false, 'status' => 'active', 'description' => 'Monthly transport allowance.'],
            ['name' => 'Phone / Internet', 'category' => 'additional', 'provider' => 'Vodacom', 'cost' => 50000, 'cost_period' => 'monthly', 'coverage' => 'Employee only', 'mandatory' => false, 'status' => 'active', 'description' => 'Monthly phone and internet allowance.'],
            ['name' => 'Meal Allowance', 'category' => 'additional', 'provider' => '', 'cost' => 75000, 'cost_period' => 'monthly', 'coverage' => 'Employee only', 'mandatory' => false, 'status' => 'inactive', 'description' => 'Monthly meal allowance.'],
        ];

        foreach ($plans as $plan) {
            BenefitPlan::create(array_merge($plan, ['client_id' => $client->id]));
        }
    }
}
