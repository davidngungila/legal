<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Department;
use App\Models\Position;

class DepartmentPositionSeeder extends Seeder
{
    public function run(): void
    {
        $client = Client::first();

        if ($client) {
            // Create departments
            $itDept = Department::create([
                'client_id' => $client->id,
                'name' => 'Information Technology',
                'code' => 'IT',
                'is_active' => true,
            ]);

            $hrDept = Department::create([
                'client_id' => $client->id,
                'name' => 'Human Resources',
                'code' => 'HR',
                'is_active' => true,
            ]);

            $financeDept = Department::create([
                'client_id' => $client->id,
                'name' => 'Finance',
                'code' => 'FINANCE',
                'is_active' => true,
            ]);

            // Create positions for each department
            Position::create([
                'client_id' => $client->id,
                'department_id' => $itDept->id,
                'title' => 'Software Developer',
                'job_code' => 'SD001',
                'is_active' => true,
            ]);

            Position::create([
                'client_id' => $client->id,
                'department_id' => $itDept->id,
                'title' => 'System Administrator',
                'job_code' => 'SA001',
                'is_active' => true,
            ]);

            Position::create([
                'client_id' => $client->id,
                'department_id' => $hrDept->id,
                'title' => 'HR Manager',
                'job_code' => 'HRM001',
                'is_active' => true,
            ]);

            Position::create([
                'client_id' => $client->id,
                'department_id' => $hrDept->id,
                'title' => 'Recruitment Specialist',
                'job_code' => 'RS001',
                'is_active' => true,
            ]);

            Position::create([
                'client_id' => $client->id,
                'department_id' => $financeDept->id,
                'title' => 'Accountant',
                'job_code' => 'AC001',
                'is_active' => true,
            ]);

            Position::create([
                'client_id' => $client->id,
                'department_id' => $financeDept->id,
                'title' => 'Financial Analyst',
                'job_code' => 'FA001',
                'is_active' => true,
            ]);
        }
    }
}
