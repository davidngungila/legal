<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;
use App\Models\LeaveEntitlement;
use App\Models\LeaveRequest;
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;
use App\Models\Employee;
use Carbon\Carbon;

class LeaveManagementDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Leave Management module...');

        $clientId = 6; // Current test client ID

        // Create standard leave types as per requirements
        $leaveTypes = [
            [
                'client_id' => $clientId,
                'type_name' => 'Annual Leave',
                'entitlement_days' => 28,
                'accrual_rate' => 2.333,
                'eligibility_months' => 6,
                'cycle_months' => 12,
                'is_paid' => true,
                'pay_rate' => 100,
                'is_active' => true,
            ],
            [
                'client_id' => $clientId,
                'type_name' => 'Sick Leave Full Pay',
                'entitlement_days' => 63,
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 36,
                'is_paid' => true,
                'pay_rate' => 100,
                'is_active' => true,
            ],
            [
                'client_id' => $clientId,
                'type_name' => 'Sick Leave Half Pay',
                'entitlement_days' => 63,
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 36,
                'is_paid' => true,
                'pay_rate' => 50,
                'is_active' => true,
            ],
            [
                'client_id' => $clientId,
                'type_name' => 'Unpaid Sick Leave',
                'entitlement_days' => 999,
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 999,
                'is_paid' => false,
                'pay_rate' => 0,
                'is_active' => true,
            ],
            [
                'client_id' => $clientId,
                'type_name' => 'Compassionate Leave',
                'entitlement_days' => 4,
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 36,
                'is_paid' => true,
                'pay_rate' => 100,
                'is_active' => true,
            ],
            [
                'client_id' => $clientId,
                'type_name' => 'Maternity Leave',
                'entitlement_days' => 84, // TELA standard
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 12,
                'is_paid' => true,
                'pay_rate' => 100,
                'is_active' => true,
            ],
            [
                'client_id' => $clientId,
                'type_name' => 'Paternity Leave',
                'entitlement_days' => 7, // TELA standard
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 12,
                'is_paid' => true,
                'pay_rate' => 100,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $lt) {
            LeaveType::firstOrCreate($lt);
        }

        $this->command->info('Leave types created.');

        // Now create entitlements for existing employees
        $employees = Employee::where('client_id', $clientId)->get();
        $allLeaveTypes = LeaveType::where('client_id', $clientId)->get();

        foreach ($employees as $emp) {
            foreach ($allLeaveTypes as $lt) {
                $cycleStart = $emp->hire_date ?? now()->subMonths(12);
                $cycleEnd = (clone $cycleStart)->addMonths($lt->cycle_months);

                $entitlementDays = $lt->entitlement_days;
                $takenDays = rand(0, min(10, $entitlementDays));

                LeaveEntitlement::firstOrCreate(
                    ['employee_id' => $emp->id, 'leave_type_id' => $lt->id],
                    [
                        'client_id' => $clientId,
                        'entitlement_days' => $entitlementDays,
                        'taken_days' => $takenDays,
                        'balance_days' => max(0, $entitlementDays - $takenDays),
                        'cycle_start' => $cycleStart,
                        'cycle_end' => $cycleEnd,
                    ]
                );
            }
        }

        $this->command->info('Leave entitlements created.');

        // Create sample leave requests
        $leaveTypesArr = $allLeaveTypes->pluck('id')->toArray();
        $statuses = ['pending', 'approved', 'rejected'];

        foreach ($employees->take(3) as $emp) {
            for ($i = 0; $i < 2; $i++) {
                $startDate = now()->subDays(rand(1, 60));
                $days = rand(1, 5);
                $endDate = (clone $startDate)->addDays($days - 1);

                $status = $statuses[array_rand($statuses)];
                $leaveTypeId = $leaveTypesArr[array_rand($leaveTypesArr)];

                $request = LeaveRequest::firstOrCreate(
                    [
                        'employee_id' => $emp->id,
                        'start_date' => $startDate,
                        'leave_type_id' => $leaveTypeId,
                    ],
                    [
                        'client_id' => $clientId,
                        'leave_type' => LeaveType::find($leaveTypeId)->type_name,
                        'end_date' => $endDate,
                        'days' => $days,
                        'days_approved' => $status === 'approved' ? $days : 0,
                        'reason' => 'Sample leave request',
                        'status' => $status,
                        'workflow_status' => $status,
                        'applied_at' => now()->subDays(2),
                        'approved_by' => $status !== 'pending' ? 1 : null,
                    ]
                );

                if ($status !== 'pending') {
                    LeaveApproval::firstOrCreate(
                        ['application_id' => $request->id],
                        [
                            'client_id' => $clientId,
                            'approver_id' => 1,
                            'action' => $status,
                            'comments' => 'Sample approval',
                            'actioned_at' => now()->subDay(),
                        ]
                    );
                }
            }
        }

        $this->command->info('Leave Management demo data created successfully!');
    }
}
