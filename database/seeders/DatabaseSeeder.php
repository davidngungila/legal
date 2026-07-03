<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\DemoPayrollAttendanceCompensationSeeder;
use Database\Seeders\CaseManagementDemoSeeder;
use Database\Seeders\LeaveManagementDemoSeeder;
use Database\Seeders\PerformanceManagementDemoSeeder;
use Database\Seeders\DisciplinaryManagementDemoSeeder;
use Database\Seeders\ExitManagementDemoSeeder;

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
            SampleClientUsersSeeder::class,
            DemoAccountsSeeder::class,
        ]);
    }
}
