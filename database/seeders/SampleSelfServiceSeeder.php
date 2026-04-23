<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SelfService;
use App\Models\Employee;
use Carbon\Carbon;

class SampleSelfServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            // Create a few sample self-service requests for each employee
            $requests = [
                [
                    'request_type' => 'leave',
                    'title' => 'Annual Leave Request',
                    'description' => 'Request for annual leave to visit family',
                    'start_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(19)->format('Y-m-d'),
                    'days_requested' => 5,
                    'status' => 'pending',
                    'request_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                ],
                [
                    'request_type' => 'payslip',
                    'title' => 'Payslip Request - ' . Carbon::now()->format('F Y'),
                    'description' => 'Request for payslip for ' . Carbon::now()->format('F Y'),
                    'status' => 'processed',
                    'request_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                    'processed_at' => Carbon::now()->subDays(3),
                ],
                [
                    'request_type' => 'contract',
                    'title' => 'Employment Contract Copy',
                    'description' => 'Request for copy of employment contract',
                    'status' => 'approved',
                    'request_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                    'approved_at' => Carbon::now()->subDays(8),
                    'approval_notes' => 'Approved by HR Manager',
                ],
            ];
            
            foreach ($requests as $requestData) {
                SelfService::firstOrCreate(
                    [
                        'client_id' => $employee->client_id,
                        'employee_id' => $employee->id,
                        'request_type' => $requestData['request_type'],
                        'title' => $requestData['title'],
                        'request_date' => $requestData['request_date'],
                    ],
                    $requestData
                );
            }
        }

        $this->command->info('Sample self-service requests seeded successfully!');
    }
}
