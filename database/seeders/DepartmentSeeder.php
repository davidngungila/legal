<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'description' => 'Manages employee relations, recruitment, and HR policies',
                'manager_name' => 'HR Director',
                'manager_email' => 'hr@legalhr.com',
                'location' => 'Headquarters',
                'employee_count' => 15,
                'status' => 'active',
            ],
            [
                'name' => 'Finance & Accounting',
                'description' => 'Handles financial planning, accounting, and budget management',
                'manager_name' => 'Finance Director',
                'manager_email' => 'finance@legalhr.com',
                'location' => 'Headquarters',
                'employee_count' => 12,
                'status' => 'active',
            ],
            [
                'name' => 'Operations',
                'description' => 'Manages day-to-day operations and production',
                'manager_name' => 'Operations Manager',
                'manager_email' => 'operations@legalhr.com',
                'location' => 'Main Office',
                'employee_count' => 25,
                'status' => 'active',
            ],
            [
                'name' => 'Sales & Marketing',
                'description' => 'Handles sales, marketing, and customer relations',
                'manager_name' => 'Sales Director',
                'manager_email' => 'sales@legalhr.com',
                'location' => 'Sales Office',
                'employee_count' => 18,
                'status' => 'active',
            ],
            [
                'name' => 'Information Technology',
                'description' => 'Manages IT infrastructure and software systems',
                'manager_name' => 'IT Manager',
                'manager_email' => 'it@legalhr.com',
                'location' => 'IT Department',
                'employee_count' => 8,
                'status' => 'active',
            ],
            [
                'name' => 'Administration',
                'description' => 'General administrative support and office management',
                'manager_name' => 'Admin Manager',
                'manager_email' => 'admin@legalhr.com',
                'location' => 'Front Office',
                'employee_count' => 6,
                'status' => 'active',
            ],
            [
                'name' => 'Production',
                'description' => 'Manufacturing and production operations',
                'manager_name' => 'Production Manager',
                'manager_email' => 'production@legalhr.com',
                'location' => 'Factory Floor',
                'employee_count' => 35,
                'status' => 'active',
            ],
            [
                'name' => 'Quality Control',
                'description' => 'Product quality assurance and testing',
                'manager_name' => 'QC Manager',
                'manager_email' => 'qc@legalhr.com',
                'location' => 'Quality Lab',
                'employee_count' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Supply Chain',
                'description' => 'Procurement and supply chain management',
                'manager_name' => 'Supply Chain Manager',
                'manager_email' => 'supply@legalhr.com',
                'location' => 'Warehouse',
                'employee_count' => 14,
                'status' => 'active',
            ],
            [
                'name' => 'Project Management',
                'description' => 'Construction project planning and execution',
                'manager_name' => 'Project Director',
                'manager_email' => 'projects@legalhr.com',
                'location' => 'Project Office',
                'employee_count' => 20,
                'status' => 'active',
            ],
            [
                'name' => 'Site Operations',
                'description' => 'On-site construction operations',
                'manager_name' => 'Site Manager',
                'manager_email' => 'site@legalhr.com',
                'location' => 'Construction Site',
                'employee_count' => 30,
                'status' => 'active',
            ],
            [
                'name' => 'Engineering',
                'description' => 'Technical and engineering services',
                'manager_name' => 'Engineering Manager',
                'manager_email' => 'engineering@legalhr.com',
                'location' => 'Engineering Office',
                'employee_count' => 16,
                'status' => 'active',
            ],
            [
                'name' => 'Software Development',
                'description' => 'Application and software development',
                'manager_name' => 'Development Manager',
                'manager_email' => 'dev@legalhr.com',
                'location' => 'Tech Hub',
                'employee_count' => 22,
                'status' => 'active',
            ],
            [
                'name' => 'Technical Support',
                'description' => 'Customer technical support and troubleshooting',
                'manager_name' => 'Support Manager',
                'manager_email' => 'support@legalhr.com',
                'location' => 'Support Center',
                'employee_count' => 12,
                'status' => 'active',
            ],
            [
                'name' => 'Medical Services',
                'description' => 'Clinical and medical services',
                'manager_name' => 'Medical Director',
                'manager_email' => 'medical@legalhr.com',
                'location' => 'Medical Center',
                'employee_count' => 28,
                'status' => 'active',
            ],
            [
                'name' => 'Nursing',
                'description' => 'Nursing and patient care services',
                'manager_name' => 'Nursing Manager',
                'manager_email' => 'nursing@legalhr.com',
                'location' => 'Ward Area',
                'employee_count' => 35,
                'status' => 'active',
            ],
            [
                'name' => 'Academic Affairs',
                'description' => 'Educational programs and curriculum',
                'manager_name' => 'Academic Director',
                'manager_email' => 'academic@legalhr.com',
                'location' => 'Academic Building',
                'employee_count' => 18,
                'status' => 'active',
            ],
            [
                'name' => 'Student Services',
                'description' => 'Student support and services',
                'manager_name' => 'Student Services Manager',
                'manager_email' => 'students@legalhr.com',
                'location' => 'Student Center',
                'employee_count' => 10,
                'status' => 'active',
            ],
        ];

        foreach ($departments as $deptData) {
            Department::firstOrCreate(
                ['name' => $deptData['name']],
                [
                    'description' => $deptData['description'],
                    'manager_name' => $deptData['manager_name'],
                    'manager_email' => $deptData['manager_email'],
                    'location' => $deptData['location'],
                    'employee_count' => $deptData['employee_count'],
                    'status' => $deptData['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Departments seeded successfully!');
    }
}
