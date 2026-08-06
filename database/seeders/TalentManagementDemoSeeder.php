<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Employee;
use App\Models\TalentPool;
use App\Models\TalentPoolMember;
use Illuminate\Database\Seeder;

class TalentManagementDemoSeeder extends Seeder
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

        if (TalentPool::withoutClientFilter()->where('client_id', $client->id)->exists()) {
            return;
        }

        $employees = Employee::where('client_id', $client->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        if ($employees->isEmpty()) {
            return;
        }

        $pools = [
            ['name' => 'Executive Leadership', 'type' => 'leadership', 'description' => 'Senior leaders ready or being groomed for executive roles.', 'readiness' => ['ready_1_2', 'developing']],
            ['name' => 'High Potentials', 'type' => 'high_potential', 'description' => 'Top performers with the potential to step into leadership.', 'readiness' => ['ready_now', 'ready_1_2']],
            ['name' => 'Future Leaders', 'type' => 'future_leader', 'description' => 'Employees positioned for advancement within 1-2 years.', 'readiness' => ['ready_1_2', 'developing']],
            ['name' => 'Technical Specialists', 'type' => 'technical', 'description' => 'Deep technical experts in critical specialist roles.', 'readiness' => ['ready_now', 'ready_1_2']],
            ['name' => 'Emerging Leaders', 'type' => 'emerging', 'description' => 'Early-career employees with high growth potential.', 'readiness' => ['developing', 'not_ready']],
        ];

        $count = $employees->count();
        $step = max(2, intdiv($count, 2));

        foreach ($pools as $index => $poolDef) {
            $pool = TalentPool::create([
                'client_id' => $client->id,
                'name' => $poolDef['name'],
                'type' => $poolDef['type'],
                'description' => $poolDef['description'],
                'status' => 'active',
                'created_by' => auth()->id() ?: null,
            ]);

            $offset = ($index * 3) % $count;
            $selected = $employees->slice($offset, $step);

            foreach ($selected as $employee) {
                $readiness = $poolDef['readiness'][array_rand($poolDef['readiness'])];
                TalentPoolMember::create([
                    'client_id' => $client->id,
                    'talent_pool_id' => $pool->id,
                    'employee_id' => $employee->id,
                    'readiness' => $readiness,
                    'notes' => null,
                    'added_by' => auth()->id() ?: null,
                ]);
            }
        }
    }
}
