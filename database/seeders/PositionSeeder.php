<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            // Human Resources Positions
            [
                'title' => 'HR Director',
                'description' => 'Oversees all HR operations and strategy',
                'department' => 'Human Resources',
                'level' => 'Executive',
                'min_salary' => 3000000,
                'max_salary' => 5000000,
                'requirements' => '10+ years HR experience, Masters degree',
                'responsibilities' => 'Strategic HR planning, team leadership, policy development',
                'reporting_to' => 'CEO',
            ],
            [
                'title' => 'HR Manager',
                'description' => 'Manages HR team and daily operations',
                'department' => 'Human Resources',
                'level' => 'Management',
                'min_salary' => 2000000,
                'max_salary' => 3500000,
                'requirements' => '5+ years HR experience, Bachelors degree',
                'responsibilities' => 'Team management, recruitment, employee relations',
                'reporting_to' => 'HR Director',
            ],
            [
                'title' => 'HR Officer',
                'description' => 'Handles recruitment and employee relations',
                'department' => 'Human Resources',
                'level' => 'Professional',
                'min_salary' => 800000,
                'max_salary' => 1500000,
                'requirements' => '2+ years HR experience, Bachelors degree',
                'responsibilities' => 'Recruitment, onboarding, employee support',
                'reporting_to' => 'HR Manager',
            ],
            [
                'title' => 'HR Assistant',
                'description' => 'Administrative support for HR department',
                'department' => 'Human Resources',
                'level' => 'Entry',
                'min_salary' => 400000,
                'max_salary' => 700000,
                'requirements' => 'Diploma, basic computer skills',
                'responsibilities' => 'Administrative tasks, record keeping',
                'reporting_to' => 'HR Officer',
            ],

            // Finance & Accounting Positions
            [
                'title' => 'Finance Director',
                'description' => 'Oversees financial strategy and planning',
                'department' => 'Finance & Accounting',
                'level' => 'Executive',
                'min_salary' => 3500000,
                'max_salary' => 6000000,
                'requirements' => 'CPA, 10+ years experience',
                'responsibilities' => 'Financial planning, budget management, reporting',
                'reporting_to' => 'CEO',
            ],
            [
                'title' => 'Accountant',
                'description' => 'Manages financial records and reporting',
                'department' => 'Finance & Accounting',
                'level' => 'Professional',
                'min_salary' => 1000000,
                'max_salary' => 2000000,
                'requirements' => 'CPA or ACCA, 3+ years experience',
                'responsibilities' => 'Financial reporting, tax compliance, audits',
                'reporting_to' => 'Finance Director',
            ],
            [
                'title' => 'Accounting Assistant',
                'description' => 'Supports accounting operations',
                'department' => 'Finance & Accounting',
                'level' => 'Entry',
                'min_salary' => 500000,
                'max_salary' => 900000,
                'requirements' => 'Diploma in Accounting, 1+ year experience',
                'responsibilities' => 'Data entry, invoice processing, basic accounting',
                'reporting_to' => 'Accountant',
            ],

            // Operations Positions
            [
                'title' => 'Operations Manager',
                'description' => 'Manages daily operations and staff',
                'department' => 'Operations',
                'level' => 'Management',
                'min_salary' => 1800000,
                'max_salary' => 3000000,
                'requirements' => '5+ years operations experience',
                'responsibilities' => 'Operations planning, team management, process improvement',
                'reporting_to' => 'COO',
            ],
            [
                'title' => 'Operations Supervisor',
                'description' => 'Supervises operational staff',
                'department' => 'Operations',
                'level' => 'Supervisor',
                'min_salary' => 1200000,
                'max_salary' => 2000000,
                'requirements' => '3+ years experience, leadership skills',
                'responsibilities' => 'Staff supervision, quality control, scheduling',
                'reporting_to' => 'Operations Manager',
            ],

            // IT Positions
            [
                'title' => 'IT Manager',
                'description' => 'Manages IT infrastructure and team',
                'department' => 'Information Technology',
                'level' => 'Management',
                'min_salary' => 2000000,
                'max_salary' => 3500000,
                'requirements' => 'Degree in IT, 5+ years experience',
                'responsibilities' => 'IT strategy, team leadership, system management',
                'reporting_to' => 'CTO',
            ],
            [
                'title' => 'Software Developer',
                'description' => 'Develops and maintains software applications',
                'department' => 'Information Technology',
                'level' => 'Professional',
                'min_salary' => 1200000,
                'max_salary' => 2500000,
                'requirements' => 'Degree in Computer Science, programming skills',
                'responsibilities' => 'Software development, testing, maintenance',
                'reporting_to' => 'IT Manager',
            ],
            [
                'title' => 'IT Support Technician',
                'description' => 'Provides technical support to users',
                'department' => 'Information Technology',
                'level' => 'Professional',
                'min_salary' => 600000,
                'max_salary' => 1200000,
                'requirements' => 'Diploma in IT, troubleshooting skills',
                'responsibilities' => 'User support, hardware/software troubleshooting',
                'reporting_to' => 'IT Manager',
            ],

            // Sales & Marketing Positions
            [
                'title' => 'Sales Director',
                'description' => 'Leads sales team and strategy',
                'department' => 'Sales & Marketing',
                'level' => 'Executive',
                'min_salary' => 2500000,
                'max_salary' => 4500000,
                'requirements' => '8+ years sales experience, leadership skills',
                'responsibilities' => 'Sales strategy, team leadership, target achievement',
                'reporting_to' => 'CEO',
            ],
            [
                'title' => 'Sales Representative',
                'description' => 'Sells products/services to customers',
                'department' => 'Sales & Marketing',
                'level' => 'Professional',
                'min_salary' => 600000,
                'max_salary' => 1200000,
                'requirements' => 'Sales experience, communication skills',
                'responsibilities' => 'Customer acquisition, sales targets, relationship management',
                'reporting_to' => 'Sales Manager',
            ],
            [
                'title' => 'Marketing Officer',
                'description' => 'Develops and executes marketing campaigns',
                'department' => 'Sales & Marketing',
                'level' => 'Professional',
                'min_salary' => 800000,
                'max_salary' => 1500000,
                'requirements' => 'Degree in Marketing, creative skills',
                'responsibilities' => 'Campaign development, market research, content creation',
                'reporting_to' => 'Marketing Manager',
            ],

            // Administration Positions
            [
                'title' => 'Office Manager',
                'description' => 'Manages office operations and supplies',
                'department' => 'Administration',
                'level' => 'Supervisor',
                'min_salary' => 800000,
                'max_salary' => 1500000,
                'requirements' => '3+ years admin experience, organizational skills',
                'responsibilities' => 'Office management, supplies, coordination',
                'reporting_to' => 'Admin Director',
            ],
            [
                'title' => 'Administrative Assistant',
                'description' => 'Provides administrative support',
                'department' => 'Administration',
                'level' => 'Entry',
                'min_salary' => 400000,
                'max_salary' => 800000,
                'requirements' => 'Diploma, computer skills',
                'responsibilities' => 'Administrative tasks, scheduling, correspondence',
                'reporting_to' => 'Office Manager',
            ],
            [
                'title' => 'Receptionist',
                'description' => 'Handles front desk and phone calls',
                'department' => 'Administration',
                'level' => 'Entry',
                'min_salary' => 350000,
                'max_salary' => 600000,
                'requirements' => 'Good communication, phone skills',
                'responsibilities' => 'Front desk management, phone handling, visitor接待',
                'reporting_to' => 'Office Manager',
            ],
        ];

        foreach ($positions as $positionData) {
            Position::firstOrCreate(
                ['title' => $positionData['title']],
                [
                    'description' => $positionData['description'],
                    'department' => $positionData['department'],
                    'level' => $positionData['level'],
                    'min_salary' => $positionData['min_salary'],
                    'max_salary' => $positionData['max_salary'],
                    'requirements' => $positionData['requirements'],
                    'responsibilities' => $positionData['responsibilities'],
                    'reporting_to' => $positionData['reporting_to'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Positions seeded successfully!');
    }
}
