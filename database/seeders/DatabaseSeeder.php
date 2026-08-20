<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            ClientSeeder::class,
            OrvionClientSeeder::class,
            UserSeeder::class,
            CreateTestUsers::class,
            AssignUsersToClients::class,
            DemoPayrollAttendanceCompensationSeeder::class,
            CaseManagementDemoSeeder::class,
            LeaveManagementDemoSeeder::class,
            PerformanceManagementDemoSeeder::class,
            DisciplinaryManagementDemoSeeder::class,
            ExitManagementDemoSeeder::class,
            DocumentSeeder::class,
            BenefitsPlansDemoSeeder::class,
            BenefitsEnrollmentsDemoSeeder::class,
            TalentManagementDemoSeeder::class,
            SuccessionDemoSeeder::class,
            SampleClientUsersSeeder::class,
            DemoAccountsSeeder::class,
        ]);
    }
}
