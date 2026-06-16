<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerformanceCycle;
use App\Models\EmployeeGoal;
use App\Models\Kpi;
use App\Models\PerformanceReview;
use App\Models\AppraisalRating;
use App\Models\PerformanceImprovementPlan;
use App\Models\PipReview;
use App\Models\CalibrationSession;
use App\Models\Employee;
use Carbon\Carbon;

class PerformanceManagementDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Performance Management module...');

        $clientId = 6;

        // Create performance cycles for current year
        $cycles = [
            [
                'client_id' => $clientId,
                'cycle_type' => 'quarterly',
                'cycle_name' => 'Q2 2026 Quarterly Review',
                'period_start' => now()->subMonths(2)->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'employee_category' => 'confirmed',
                'status' => 'active',
            ],
            [
                'client_id' => $clientId,
                'cycle_type' => 'monthly',
                'cycle_name' => 'June 2026 Probation Review',
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'employee_category' => 'probation',
                'status' => 'active',
            ],
        ];

        foreach ($cycles as $c) {
            PerformanceCycle::firstOrCreate($c);
        }

        $this->command->info('Performance cycles created.');

        $employees = Employee::where('client_id', $clientId)->get();
        $cycle = PerformanceCycle::where('client_id', $clientId)->first();

        foreach ($employees->take(3) as $emp) {
            // Create a goal for this employee
            $goal = EmployeeGoal::firstOrCreate(
                ['employee_id' => $emp->id, 'cycle_id' => $cycle->id],
                [
                    'client_id' => $clientId,
                    'goal_title' => 'Achieve quarterly targets',
                    'description' => 'Meet all quarterly KPIs',
                    'kpi_count' => 2,
                    'weight_total' => 100,
                    'status' => 'approved',
                    'approved_by' => 1,
                ]
            );

            // Create sample KPIs
            Kpi::firstOrCreate(
                ['goal_id' => $goal->id, 'kpi_description' => 'Revenue target'],
                [
                    'target' => 'TZS 50,000,000',
                    'weight' => 60,
                    'measurement_unit' => 'TZS',
                    'deadline' => now()->endOfMonth(),
                ]
            );

            Kpi::firstOrCreate(
                ['goal_id' => $goal->id, 'kpi_description' => 'Client satisfaction score'],
                [
                    'target' => '90%',
                    'weight' => 40,
                    'measurement_unit' => '%',
                    'deadline' => now()->endOfMonth(),
                ]
            );

            // Create performance review
            $review = PerformanceReview::firstOrCreate(
                ['employee_id' => $emp->id, 'cycle_id' => $cycle->id],
                [
                    'client_id' => $clientId,
                    'reviewer_id' => 1,
                    'review_date' => now(),
                    'self_rating' => 85,
                    'supervisor_rating' => 80,
                    'calibrated_rating' => 82,
                    'final_rating' => 82,
                    'rating' => 82,
                    'comments' => 'Good performance this quarter',
                    'goals' => json_encode(['Goal 1', 'Goal 2']),
                    'status' => 'finalized',
                    'completed_at' => now(),
                ]
            );

            // Create appraisal ratings per KPI
            $kpis = $goal->kpis;
            foreach ($kpis as $k) {
                AppraisalRating::firstOrCreate(
                    ['appraisal_id' => $review->id, 'kpi_id' => $k->id],
                    [
                        'self_score' => 85,
                        'supervisor_score' => 80,
                        'calibrated_score' => 82,
                        'comments' => 'Good work on ' . $k->kpi_description,
                    ]
                );
            }
        }

        // Create sample PIP for one employee
        $pipEmp = $employees->first();
        if ($pipEmp) {
            $pip = PerformanceImprovementPlan::firstOrCreate(
                ['employee_id' => $pipEmp->id, 'start_date' => now()->subMonth()],
                [
                    'client_id' => $clientId,
                    'pip_objectives' => 'Improve punctuality and task completion rate',
                    'start_date' => now()->subMonth(),
                    'end_date' => now()->addMonth(),
                    'review_frequency' => 'biweekly',
                    'status' => 'active',
                ]
            );

            PipReview::firstOrCreate(
                ['pip_id' => $pip->id, 'review_date' => now()->subWeeks(2)],
                [
                    'reviewer_id' => 1,
                    'progress_rating' => 60,
                    'comments' => 'Some improvement, needs more consistency',
                    'action_items' => 'Improve time management',
                ]
            );
        }

        // Create calibration session
        CalibrationSession::firstOrCreate(
            ['client_id' => $clientId, 'cycle_id' => $cycle->id],
            [
                'facilitated_by' => 1,
                'session_date' => now()->addDays(5),
                'notes' => 'Session to calibrate Q2 ratings',
                'status' => 'planned',
            ]
        );

        $this->command->info('Performance Management demo data created successfully!');
    }
}
