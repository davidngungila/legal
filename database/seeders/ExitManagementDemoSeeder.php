<?php

namespace Database\Seeders;

use App\Models\ExitCase;
use App\Models\ExitChecklist;
use App\Models\ExitSettlement;
use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExitManagementDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Exit Management Module...');

        $clients = Client::all();

        foreach ($clients as $client) {
            $employees = Employee::where('client_id', $client->id)->take(3)->get();
            if ($employees->isEmpty()) {
                continue;
            }

            $user = User::where('current_client_id', $client->id)->first() ?? User::first();

            // Resignation case
            $resignation = ExitCase::firstOrCreate(
                ['client_id' => $client->id, 'exit_number' => 'EXIT-' . now()->format('Y') . '-001'],
                [
                    'employee_id' => $employees[0]->id,
                    'exit_type' => 'resignation',
                    'exit_date' => now()->addDays(30),
                    'notice_date' => now(),
                    'reason' => 'Personal reasons, moving to another city.',
                    'status' => 'processing',
                    'initiated_by' => $user->id,
                ]
            );

            $checklistItems = [
                ['item_name' => 'Return laptop and accessories', 'category' => 'company_property'],
                ['item_name' => 'Return access card and keys', 'category' => 'company_property'],
                ['item_name' => 'Revoke system access', 'category' => 'it_clearance'],
                ['item_name' => 'Complete handover notes', 'category' => 'work_handover'],
                ['item_name' => 'Process final payslip', 'category' => 'payroll'],
            ];

            foreach ($checklistItems as $item) {
                ExitChecklist::firstOrCreate(
                    ['exit_case_id' => $resignation->id, 'item_name' => $item['item_name']],
                    array_merge($item, ['completed' => false])
                );
            }

            ExitSettlement::firstOrCreate(
                ['exit_case_id' => $resignation->id],
                [
                    'final_salary' => 1200000,
                    'leave_pay' => 300000,
                    'notice_pay' => 0,
                    'bonus_pay' => 0,
                    'other_payments' => 0,
                    'total_settlement' => 1500000,
                    'status' => 'pending',
                ]
            );

            // Retirement case
            $retirement = ExitCase::firstOrCreate(
                ['client_id' => $client->id, 'exit_number' => 'EXIT-' . now()->format('Y') . '-002'],
                [
                    'employee_id' => $employees[1]->id,
                    'exit_type' => 'retirement',
                    'exit_date' => now()->addMonths(2),
                    'notice_date' => now(),
                    'reason' => 'Retirement at statutory age.',
                    'status' => 'initiated',
                    'initiated_by' => $user->id,
                ]
            );

            $this->command->info('Exit cases created for client: ' . $client->name);
        }

        $this->command->info('Exit Management demo data created successfully!');
    }
}
