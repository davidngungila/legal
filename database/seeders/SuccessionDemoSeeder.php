<?php

namespace Database\Seeders;

use App\Models\CareerPath;
use App\Models\CareerPathLevel;
use App\Models\CareerPathMember;
use App\Models\Client;
use App\Models\Employee;
use App\Models\SuccessionReadiness;
use Illuminate\Database\Seeder;

class SuccessionDemoSeeder extends Seeder
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

        if (SuccessionReadiness::withoutClientFilter()->where('client_id', $client->id)->exists()
            || CareerPath::withoutClientFilter()->where('client_id', $client->id)->exists()) {
            return;
        }

        $employees = Employee::where('client_id', $client->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        if ($employees->isEmpty()) {
            return;
        }

        $roles = [
            ['HR Assistant', 'HR Officer', 'Senior HR Officer', 'HR Manager', 'Head of HR'],
            ['Accounts Clerk', 'Accountant', 'Senior Accountant', 'Finance Manager', 'Chief Finance Officer'],
            ['IT Support', 'Systems Administrator', 'IT Manager', 'Head of ICT'],
            ['Front Office Assistant', 'Customer Relations Officer', 'Operations Supervisor', 'Operations Manager'],
            ['Legal Assistant', 'Paralegal', 'Associate Lawyer', 'Senior Associate', 'Partner'],
        ];

        $readinessFlags = ['ready_now', 'ready_1_2', 'ready_2_3', 'development'];
        $count = $employees->count();

        foreach ($employees->take(min(6, $count)) as $index => $employee) {
            SuccessionReadiness::create([
                'client_id' => $client->id,
                'employee_id' => $employee->id,
                'current_role' => $employee->position ?: 'Current Role',
                'readiness' => $readinessFlags[$index % count($readinessFlags)],
                'target_role' => 'Successor for ' . ($roles[0][min($index + 1, 4)] ?? 'Key Role'),
                'development_needs' => $index % 2 === 0 ? 'Leadership and stakeholder management training.' : null,
                'assessment_date' => now()->subDays($index * 5),
                'status' => 'active',
            ]);
        }

        $departments = ['Human Resources', 'Finance', 'ICT', 'Operations', 'Legal'];

        foreach ($roles as $index => $track) {
            $path = CareerPath::create([
                'client_id' => $client->id,
                'name' => $track[0] . ' Career Track',
                'department' => $departments[$index] ?? null,
                'description' => 'Career progression path from entry level to ' . end($track) . '.',
                'status' => $index === 4 ? 'inactive' : 'active',
            ]);

            $levelIds = [];
            foreach ($track as $order => $title) {
                $level = CareerPathLevel::create([
                    'client_id' => $client->id,
                    'career_path_id' => $path->id,
                    'level_order' => $order + 1,
                    'title' => $title,
                    'typical_time' => $order < count($track) - 1 ? (1 + $order) . '-' . (2 + $order) . ' years' : null,
                    'competencies' => 'Leadership, communication, domain expertise',
                    'responsibilities' => 'Core duties for the ' . $title . ' role.',
                ]);
                $levelIds[] = $level->id;
            }

            $offset = ($index * 2) % $count;
            $members = $employees->slice($offset, min(3, $count));

            foreach ($members as $member) {
                CareerPathMember::create([
                    'client_id' => $client->id,
                    'career_path_id' => $path->id,
                    'employee_id' => $member->id,
                    'current_level_order' => rand(1, min(3, count($track))),
                ]);
            }
        }
    }
}
