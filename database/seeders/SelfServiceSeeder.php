<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SelfService;
use App\Models\Employee;
use Carbon\Carbon;

class SelfServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            // Create various self-service requests for each employee
            $this->createLeaveRequests($employee);
            $this->createPayslipRequests($employee);
            $this->createContractRequests($employee);
            $this->createComplaints($employee);
            $this->createExpenseClaims($employee);
        }

        $this->command->info('Self-service requests seeded successfully for all employees!');
    }
    
    /**
     * Create leave requests
     */
    private function createLeaveRequests($employee)
    {
        $leaveRequests = [
            [
                'title' => 'Annual Leave Request',
                'description' => 'Request for annual leave to visit family upcountry',
                'start_date' => Carbon::now()->addDays(rand(10, 30))->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(rand(35, 45))->format('Y-m-d'),
                'days_requested' => rand(5, 10),
                'status' => 'approved',
                'request_date' => Carbon::now()->subDays(rand(5, 15))->format('Y-m-d'),
            ],
            [
                'title' => 'Sick Leave Request',
                'description' => 'Medical checkup and recovery',
                'start_date' => Carbon::now()->subDays(rand(20, 30))->format('Y-m-d'),
                'end_date' => Carbon::now()->subDays(rand(18, 25))->format('Y-m-d'),
                'days_requested' => rand(2, 5),
                'status' => 'processed',
                'request_date' => Carbon::now()->subDays(rand(25, 35))->format('Y-m-d'),
            ],
        ];
        
        foreach ($leaveRequests as $request) {
            SelfService::firstOrCreate(
                [
                    'client_id' => $employee->client_id,
                    'employee_id' => $employee->id,
                    'request_type' => 'leave',
                    'title' => $request['title'],
                    'request_date' => $request['request_date'],
                ],
                [
                    'description' => $request['description'],
                    'start_date' => $request['start_date'],
                    'end_date' => $request['end_date'],
                    'days_requested' => $request['days_requested'],
                    'status' => $request['status'],
                    'approved_at' => $request['status'] === 'approved' ? Carbon::now()->subDays(rand(1, 5)) : null,
                    'approval_notes' => $request['status'] === 'approved' ? 'Approved by HR Manager' : null,
                ]
            );
        }
    }
    
    /**
     * Create payslip requests
     */
    private function createPayslipRequests($employee)
    {
        for ($month = 1; $month <= 3; $month++) {
            $requestDate = Carbon::now()->subMonths($month);
            
            SelfService::firstOrCreate(
                [
                    'client_id' => $employee->client_id,
                    'employee_id' => $employee->id,
                    'request_type' => 'payslip',
                    'title' => 'Payslip Request - ' . $requestDate->format('F Y'),
                    'request_date' => $requestDate->format('Y-m-d'),
                ],
                [
                    'description' => 'Request for payslip for ' . $requestDate->format('F Y'),
                    'status' => 'processed',
                    'processed_at' => $requestDate->addDays(2),
                ]
            );
        }
    }
    
    /**
     * Create contract requests
     */
    private function createContractRequests($employee)
    {
        SelfService::firstOrCreate(
            [
                'client_id' => $employee->client_id,
                'employee_id' => $employee->id,
                'request_type' => 'contract',
                'title' => 'Employment Contract Copy',
                'request_date' => Carbon::now()->subDays(rand(10, 20))->format('Y-m-d'),
            ],
            [
                'description' => 'Request for copy of employment contract for personal records',
                'status' => 'processed',
                'processed_at' => Carbon::now()->subDays(rand(5, 15)),
            ]
        );
    }
    
    /**
     * Create complaints
     */
    private function createComplaints($employee)
    {
        if (rand(1, 10) <= 3) { // 30% chance of having a complaint
            $complaints = [
                [
                    'title' => 'Work Environment Issue',
                    'description' => 'Air conditioning not working properly in the office',
                    'status' => 'pending',
                ],
                [
                    'title' => 'Equipment Request',
                    'description' => 'Need new laptop as current one is slow and outdated',
                    'status' => 'approved',
                ],
                [
                    'title' => 'Schedule Concern',
                    'description' => 'Request for flexible working hours due to family commitments',
                    'status' => 'rejected',
                ],
            ];
            
            $complaint = $complaints[array_rand($complaints)];
            
            SelfService::firstOrCreate(
                [
                    'client_id' => $employee->client_id,
                    'employee_id' => $employee->id,
                    'request_type' => 'complaint',
                    'title' => $complaint['title'],
                    'request_date' => Carbon::now()->subDays(rand(1, 10))->format('Y-m-d'),
                ],
                [
                    'description' => $complaint['description'],
                    'status' => $complaint['status'],
                    'approved_at' => $complaint['status'] === 'approved' ? Carbon::now()->subDays(rand(1, 5)) : null,
                    'rejection_reason' => $complaint['status'] === 'rejected' ? 'Not approved due to company policy' : null,
                ]
            );
        }
    }
    
    /**
     * Create expense claims
     */
    private function createExpenseClaims($employee)
    {
        if (rand(1, 10) <= 4) { // 40% chance of having expense claims
            $expenses = [
                [
                    'title' => 'Travel Expenses',
                    'description' => 'Transport costs for client meeting',
                    'amount' => rand(50000, 150000),
                    'status' => 'approved',
                ],
                [
                    'title' => 'Training Materials',
                    'description' => 'Purchase of books and materials for professional development',
                    'amount' => rand(80000, 200000),
                    'status' => 'processed',
                ],
                [
                    'title' => 'Office Supplies',
                    'description' => 'Stationery and office supplies for department',
                    'amount' => rand(30000, 100000),
                    'status' => 'pending',
                ],
            ];
            
            $expense = $expenses[array_rand($expenses)];
            
            SelfService::firstOrCreate(
                [
                    'client_id' => $employee->client_id,
                    'employee_id' => $employee->id,
                    'request_type' => 'expense_claim',
                    'title' => $expense['title'],
                    'request_date' => Carbon::now()->subDays(rand(1, 15))->format('Y-m-d'),
                ],
                [
                    'description' => $expense['description'],
                    'amount' => $expense['amount'],
                    'status' => $expense['status'],
                    'approved_at' => $expense['status'] === 'approved' ? Carbon::now()->subDays(rand(1, 7)) : null,
                    'approval_notes' => $expense['status'] === 'approved' ? 'Expense approved with receipt verification' : null,
                ]
            );
        }
    }
}
