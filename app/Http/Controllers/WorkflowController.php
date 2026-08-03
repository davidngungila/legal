<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\EmployeeRegistration;
use App\Models\JobVacancy;
use App\Models\HrCompetencyInterview;
use App\Models\TechnicalInterview;
use App\Models\WorkflowComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class WorkflowController extends Controller
{
    /**
     * Registered workflow types that participate in the approval hub.
     *
     * @var array<string, array{model: string, label: string, pending: string[], approved: string}>
     */
    private const WORKFLOW_TYPES = [
        'employee_registration' => [
            'model' => EmployeeRegistration::class,
            'label' => 'Employee Registration',
            'pending' => ['submitted'],
            'approved' => 'approved',
        ],
        'job_vacancy' => [
            'model' => JobVacancy::class,
            'label' => 'Job Vacancy',
            'pending' => ['submitted', 'supervisor_approved', 'manager_recommended'],
            'approved' => 'hr_approved',
        ],
        'hr_interview' => [
            'model' => HrCompetencyInterview::class,
            'label' => 'HR Interview',
            'pending' => ['submitted'],
            'approved' => 'hr_approved',
        ],
        'technical_interview' => [
            'model' => TechnicalInterview::class,
            'label' => 'Technical Interview',
            'pending' => ['submitted', 'interviewer_completed'],
            'approved' => 'manager_approved',
        ],
    ];

    /**
     * Display the workflow dashboard.
     */
    public function index()
    {
        return view('hris.workflow.index');
    }

    /**
     * Get workflow statistics.
     */
    public function statistics()
    {
        try {
            $clientId = session('current_client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }

            $stats = [
                'pending_approvals' => 0,
                'approved_today' => 0,
                'rejected_today' => 0,
                'overdue_approvals' => 0,
                'total_workflows' => 0,
                'by_type' => [],
                'by_status' => ['pending' => 0, 'approved' => 0, 'rejected' => 0],
                'avg_approval_time_hours' => 0,
                'approval_rate' => 0,
            ];

            $approvedTotal = 0;
            $decidedTotal = 0;
            $approvalHours = [];

            foreach (self::WORKFLOW_TYPES as $type => $config) {
                $model = $config['model'];

                $total = $model::where('client_id', $clientId)->count();
                $pending = $model::where('client_id', $clientId)->whereIn('status', $config['pending'])->count();
                $approved = $model::where('client_id', $clientId)->where('status', $config['approved'])->count();
                $rejected = $model::where('client_id', $clientId)->where('status', 'rejected')->count();

                $stats['by_type'][$type] = $total;
                $stats['by_status']['pending'] += $pending;
                $stats['by_status']['approved'] += $approved;
                $stats['by_status']['rejected'] += $rejected;
                $stats['pending_approvals'] += $pending;
                $stats['total_workflows'] += $total;

                $approvedTotal += $approved;
                $decidedTotal += $approved + $rejected;

                // Approved today / rejected today / overdue are only meaningful for items in the
                // pending + decided pool, tracked via the audit timestamps on the records.
                $stats['approved_today'] += $model::where('client_id', $clientId)
                    ->where('status', $config['approved'])
                    ->whereDate($this->approvedAtColumn($type), now()->toDateString())
                    ->count();
                $stats['rejected_today'] += $model::where('client_id', $clientId)
                    ->where('status', 'rejected')
                    ->whereDate('updated_at', now()->toDateString())
                    ->count();
                $stats['overdue_approvals'] += $model::where('client_id', $clientId)
                    ->whereIn('status', $config['pending'])
                    ->where('created_at', '<', now()->subDays(3))
                    ->count();

                // Average approval time from real records
                $model::where('client_id', $clientId)
                    ->whereNotNull($this->approvedAtColumn($type))
                    ->get()
                    ->each(function ($record) use (&$approvalHours, $type) {
                        $hours = $this->recordApprovalHours($record, $this->approvedAtColumn($type));
                        if ($hours !== null) {
                            $approvalHours[] = $hours;
                        }
                    });
            }

            $stats['approval_rate'] = $decidedTotal > 0 ? round($approvedTotal / $decidedTotal * 100, 1) : 0;
            $stats['avg_approval_time_hours'] = count($approvalHours) > 0
                ? round(array_sum($approvalHours) / count($approvalHours), 1)
                : 0;

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow statistics retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending approvals for the current user.
     */
    public function pendingApprovals()
    {
        try {
            $clientId = session('current_client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }

            $approvals = [];

            foreach (self::WORKFLOW_TYPES as $type => $config) {
                $model = $config['model'];
                $records = $model::where('client_id', $clientId)
                    ->whereIn('status', $config['pending'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($records as $record) {
                    $approvals[] = $this->pendingPayload($type, $record);
                }
            }

            // Sort so the oldest / highest-priority items surface first
            usort($approvals, fn ($a, $b) => strcmp($a['submitted_at'], $b['submitted_at']));

            return response()->json([
                'success' => true,
                'approvals' => $approvals
            ]);

        } catch (\Exception $e) {
            Log::error('Pending approvals retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workflow history.
     */
    public function history()
    {
        try {
            $clientId = session('current_client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }

            $history = [];

            foreach (self::WORKFLOW_TYPES as $type => $config) {
                $model = $config['model'];
                $records = $model::where('client_id', $clientId)
                    ->whereIn('status', [$config['approved'], 'rejected'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(10)
                    ->get();

                foreach ($records as $record) {
                    $history[] = $this->historyPayload($type, $record);
                }
            }

            usort($history, fn ($a, $b) => strcmp($b['performed_at'], $a['performed_at']));

            return response()->json([
                'success' => true,
                'history' => array_slice($history, 0, 20)
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow history retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a workflow item.
     */
    public function approve(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workflow_id' => 'required|integer',
            'workflow_type' => 'nullable|string',
            'comments' => 'nullable|string|max:1000',
            'next_approver' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            [$record, $type] = $this->resolve($request->workflow_id, $request->workflow_type);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow item not found'
                ], 404);
            }

            $oldStatus = $record->status;
            $this->applyApproval($type, $record);
            $newStatus = $record->status;

            AuditLogger::log(
                'workflow.approved',
                $record,
                'Workflow',
                "{$this->labelFor($type)} #{$record->id} approved ({$oldStatus} → {$newStatus})" . ($request->comments ? ' - ' . $request->comments : ''),
                ['status' => $oldStatus],
                ['status' => $newStatus, 'approved_by' => auth()->id()]
            );

            if ($request->comments) {
                $this->storeComment($type, $record->id, $request->comments);
            }

            return response()->json([
                'success' => true,
                'message' => "{$this->labelFor($type)} approved successfully",
                'data' => [
                    'workflow_id' => $record->id,
                    'workflow_type' => $type,
                    'status' => $newStatus,
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'comments' => $request->comments,
                    'next_approver' => $request->next_approver,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a workflow item.
     */
    public function reject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workflow_id' => 'required|integer',
            'workflow_type' => 'nullable|string',
            'reason' => 'required|string|max:1000',
            'allow_resubmission' => 'required|boolean',
            'resubmission_instructions' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            [$record, $type] = $this->resolve($request->workflow_id, $request->workflow_type);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow item not found'
                ], 404);
            }

            $oldStatus = $record->status;

            $record->update([
                'status' => 'rejected',
                'updated_by' => auth()->id(),
            ]);

            // Persist rejection details where the model supports JSON metadata
            if ($type === 'employee_registration') {
                $record->update([
                    'ranking_details' => array_merge($record->ranking_details ?? [], [
                        'rejection_reason' => $request->reason,
                        'allow_resubmission' => $request->boolean('allow_resubmission'),
                        'resubmission_instructions' => $request->resubmission_instructions,
                    ]),
                ]);
            }

            AuditLogger::log(
                'workflow.rejected',
                $record,
                'Workflow',
                "{$this->labelFor($type)} #{$record->id} rejected ({$oldStatus} → rejected) - {$request->reason}",
                ['status' => $oldStatus],
                ['status' => 'rejected', 'reason' => $request->reason, 'rejected_by' => auth()->id()]
            );

            // Persist the reason as a workflow comment so it surfaces in the details view
            $this->storeComment($type, $record->id, 'Rejected: ' . $request->reason
                . ($request->boolean('allow_resubmission') ? ' (Resubmission allowed' . ($request->resubmission_instructions ? ': ' . $request->resubmission_instructions : '') . ')' : ''));

            return response()->json([
                'success' => true,
                'message' => "{$this->labelFor($type)} rejected successfully",
                'data' => [
                    'workflow_id' => $record->id,
                    'workflow_type' => $type,
                    'rejected_by' => auth()->id(),
                    'rejected_at' => now(),
                    'reason' => $request->reason,
                    'allow_resubmission' => $request->boolean('allow_resubmission'),
                    'resubmission_instructions' => $request->resubmission_instructions,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow rejection failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forward a workflow item to another approver.
     */
    public function forward(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workflow_id' => 'required|integer',
            'workflow_type' => 'nullable|string',
            'forward_to' => 'required|string|max:255',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            [$record, $type] = $this->resolve($request->workflow_id, $request->workflow_type);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow item not found'
                ], 404);
            }

            // Persist forward details where the model supports JSON metadata
            if ($type === 'employee_registration') {
                $record->update([
                    'updated_by' => auth()->id(),
                    'ranking_details' => array_merge($record->ranking_details ?? [], [
                        'forwarded_to' => $request->forward_to,
                        'forwarded_by' => auth()->user()->name,
                        'forwarded_at' => now()->format('Y-m-d H:i:s'),
                        'forward_reason' => $request->reason,
                    ]),
                ]);
            } else {
                $record->update(['updated_by' => auth()->id()]);
            }

            AuditLogger::log(
                'workflow.forwarded',
                $record,
                'Workflow',
                "{$this->labelFor($type)} #{$record->id} forwarded to {$request->forward_to} - {$request->reason}",
                null,
                ['forwarded_to' => $request->forward_to, 'forwarded_by' => auth()->id()]
            );

            $this->storeComment($type, $record->id, "Forwarded to {$request->forward_to} by " . auth()->user()->name . ': ' . $request->reason);

            return response()->json([
                'success' => true,
                'message' => "{$this->labelFor($type)} forwarded successfully",
                'data' => [
                    'workflow_id' => $record->id,
                    'workflow_type' => $type,
                    'forwarded_by' => auth()->id(),
                    'forwarded_at' => now(),
                    'forwarded_to' => $request->forward_to,
                    'reason' => $request->reason,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow forwarding failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workflow details.
     */
    public function details($workflowId, Request $request)
    {
        try {
            $clientId = session('current_client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }

            [$record, $type] = $this->resolve($workflowId, $request->query('type'));

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow item not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'details' => $this->detailsPayload($type, $record)
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow details retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add comment to workflow.
     */
    public function addComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workflow_id' => 'required|integer',
            'workflow_type' => 'required|string',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comment = $this->storeComment($request->workflow_type, $request->workflow_id, $request->comment);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => [
                    'id' => $comment->id,
                    'workflow_id' => $comment->workflow_id,
                    'workflow_type' => $comment->workflow_type,
                    'comment' => $comment->comment,
                    'author' => auth()->user()->name,
                    'created_at' => $comment->created_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow comment addition failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workflow calendar.
     */
    public function calendar()
    {
        try {
            $clientId = session('current_client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }

            $events = [];

            foreach (self::WORKFLOW_TYPES as $type => $config) {
                $model = $config['model'];
                $records = $model::where('client_id', $clientId)
                    ->whereIn('status', $config['pending'])
                    ->get();

                foreach ($records as $record) {
                    $events[] = $this->calendarEvent($type, $record);
                }
            }

            // Contract renewals (placeholder deadline from active contracts)
            $contracts = \App\Models\Contract::where('client_id', $clientId)
                ->where('status', 'active')
                ->whereNotNull('end_date')
                ->whereBetween('end_date', [now(), now()->addDays(60)])
                ->limit(5)
                ->get();

            foreach ($contracts as $contract) {
                $events[] = [
                    'title' => 'Contract Renewal - ' . ($contract->employee ? trim($contract->employee->first_name . ' ' . $contract->employee->last_name) : 'Employee'),
                    'start' => $contract->end_date->format('Y-m-d'),
                    'type' => 'renewal',
                    'priority' => 'medium',
                    'workflow_id' => $contract->id,
                    'workflow_type' => 'contract_management',
                ];
            }

            usort($events, fn ($a, $b) => strcmp($a['start'], $b['start']));

            return response()->json([
                'success' => true,
                'events' => $events
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow calendar retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workflow analytics.
     */
    public function analytics()
    {
        try {
            $clientId = session('current_client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }

            $trends = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $trends[] = [
                    'month' => $month->format('M'),
                    'approved' => 0,
                    'rejected' => 0,
                    'pending' => 0,
                ];
            }

            $approvalTimes = [];
            $bottlenecks = [];
            $approvers = [];

            foreach (self::WORKFLOW_TYPES as $type => $config) {
                $model = $config['model'];

                $approvalTimes[$type] = $this->averageApprovalHours($model, $clientId, $this->approvedAtColumn($type));

                foreach ($trends as $idx => &$trend) {
                    $month = now()->subMonths(5 - $idx);
                    $trend['approved'] += $model::where('client_id', $clientId)
                        ->where('status', $config['approved'])
                        ->whereMonth($this->approvedAtColumn($type), $month->month)
                        ->whereYear($this->approvedAtColumn($type), $month->year)
                        ->count();
                    $trend['rejected'] += $model::where('client_id', $clientId)
                        ->where('status', 'rejected')
                        ->whereMonth('updated_at', $month->month)
                        ->whereYear('updated_at', $month->year)
                        ->count();
                    $trend['pending'] += $model::where('client_id', $clientId)
                        ->whereIn('status', $config['pending'])
                        ->whereMonth('created_at', $month->month)
                        ->whereYear('created_at', $month->year)
                        ->count();
                }
                unset($trend);

                $queue = $model::where('client_id', $clientId)->whereIn('status', $config['pending'])->count();
                $bottlenecks[] = [
                    'step' => $this->approvalStepLabel($type),
                    'avg_time' => $this->averageApprovalHours($model, $clientId, $this->approvedAtColumn($type)),
                    'queue_size' => $queue,
                    'type' => $type,
                ];
            }

            // Approver performance based on records handled
            $employeeRegs = EmployeeRegistration::where('client_id', $clientId)
                ->whereNotNull('approved_by')
                ->get()
                ->groupBy('approved_by');
            foreach ($employeeRegs as $userId => $records) {
                $user = \App\Models\User::find($userId);
                $approvers[] = [
                    'approver' => $user ? $user->name : 'System',
                    'approved' => $records->count(),
                    'rejected' => 0,
                    'avg_time' => 0,
                ];
            }

            return response()->json([
                'success' => true,
                'analytics' => [
                    'approval_trends' => $trends,
                    'approval_times' => $approvalTimes,
                    'approver_performance' => $approvers,
                    'bottlenecks' => $bottlenecks,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow analytics retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /* --------------------------------------------------------------------------
     | Helpers
     | -------------------------------------------------------------------------- */

    /**
     * Resolve a workflow record across registered types.
     *
     * @return array{0: object|null, 1: string|null}
     */
    private function resolve($id, ?string $type): array
    {
        if ($type && isset(self::WORKFLOW_TYPES[$type])) {
            $record = self::WORKFLOW_TYPES[$type]['model']::find($id);
            return [$record, $type];
        }

        foreach (self::WORKFLOW_TYPES as $key => $config) {
            $record = $config['model']::find($id);
            if ($record) {
                return [$record, $key];
            }
        }

        return [null, null];
    }

    /**
     * Apply the correct approval transition for the given workflow type.
     */
    private function applyApproval(string $type, $record): void
    {
        switch ($type) {
            case 'employee_registration':
                $record->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'updated_by' => auth()->id(),
                ]);
                break;

            case 'job_vacancy':
                $data = ['updated_by' => auth()->id()];
                if ($record->status === 'submitted') {
                    $data['status'] = 'supervisor_approved';
                    $data['supervisor_approved_at'] = now();
                } elseif ($record->status === 'supervisor_approved') {
                    $data['status'] = 'manager_recommended';
                    $data['manager_recommended_at'] = now();
                } elseif ($record->status === 'manager_recommended') {
                    $data['status'] = 'hr_approved';
                    $data['hr_approved_at'] = now();
                }
                $record->update($data);
                break;

            case 'hr_interview':
                $record->update([
                    'status' => 'hr_approved',
                    'hr_manager_id' => auth()->id(),
                    'hr_manager_approved_at' => now(),
                    'updated_by' => auth()->id(),
                ]);
                break;

            case 'technical_interview':
                $record->update([
                    'status' => 'manager_approved',
                    'manager_approval' => 'approved',
                    'manager_approved_at' => now(),
                    'updated_by' => auth()->id(),
                ]);
                break;
        }
    }

    private function pendingPayload(string $type, $record): array
    {
        switch ($type) {
            case 'employee_registration':
                $title = 'Employee Registration - ' . $record->first_name . ' ' . $record->surname;
                $description = 'New employee registration requiring final approval';
                $submittedBy = $record->creator?->name ?? 'System';
                $priority = 'high';
                $step = 3;
                $total = 3;
                $stepLabel = 'Final Approval';
                break;
            case 'job_vacancy':
                $title = 'Job Vacancy - ' . $record->job_title;
                $description = 'New job vacancy requiring approval';
                $submittedBy = $record->initiator?->name ?? 'Manager';
                $priority = 'medium';
                $total = 4;
                $step = match ($record->status) {
                    'supervisor_approved' => 3,
                    'manager_recommended' => 4,
                    default => 2,
                };
                $stepLabel = match ($record->status) {
                    'supervisor_approved' => 'Manager Review',
                    'manager_recommended' => 'HR Approval',
                    default => 'Supervisor Review',
                };
                break;
            case 'hr_interview':
                $title = 'HR Interview - ' . $record->candidate_name;
                $description = 'HR interview results requiring review';
                $submittedBy = $record->interviewer?->name ?? 'HR';
                $priority = 'medium';
                $step = 1;
                $total = 2;
                $stepLabel = 'Manager Review';
                break;
            case 'technical_interview':
                $title = 'Technical Interview - ' . $record->candidate_name;
                $description = 'Technical interview results requiring manager approval';
                $submittedBy = $record->interviewer?->name ?? 'Interviewer';
                $priority = $record->status === 'interviewer_completed' ? 'high' : 'medium';
                $step = $record->status === 'interviewer_completed' ? 2 : 1;
                $total = 3;
                $stepLabel = $record->status === 'interviewer_completed' ? 'Manager Approval' : 'Technical Assessment';
                break;
            default:
                $title = $record->id;
                $description = 'Workflow item requiring approval';
                $submittedBy = 'System';
                $priority = 'medium';
                $step = 1;
                $total = 1;
                $stepLabel = 'Review';
        }

        return [
            'id' => $record->id,
            'type' => $type,
            'type_label' => $this->labelFor($type),
            'title' => $title,
            'description' => $description,
            'submitted_by' => $submittedBy,
            'submitted_at' => $record->created_at->format('Y-m-d H:i:s'),
            'priority' => $priority,
            'status' => $record->status,
            'current_approver' => auth()->user()->name,
            'workflow_step' => $stepLabel,
            'total_steps' => $total,
            'current_step' => $step,
        ];
    }

    private function historyPayload(string $type, $record): array
    {
        $action = $record->status === 'rejected' ? 'rejected' : 'approved';

        switch ($type) {
            case 'employee_registration':
                $title = 'Registration - ' . $record->first_name . ' ' . $record->surname;
                $performedBy = $record->approver?->name ?? 'System';
                $comments = $record->ranking_details['rejection_reason'] ?? 'Processed via standard workflow';
                break;
            case 'job_vacancy':
                $title = 'Vacancy - ' . $record->job_title;
                $performedBy = $record->hrManager?->name ?? ($record->manager?->name ?? 'System');
                $comments = $action === 'approved' ? 'HR review completed' : 'Rejected during review';
                break;
            case 'hr_interview':
                $title = 'HR Interview - ' . $record->candidate_name;
                $performedBy = $record->hrManager?->name ?? 'System';
                $comments = $action === 'approved' ? 'HR approved' : 'Rejected during review';
                break;
            case 'technical_interview':
                $title = 'Technical Interview - ' . $record->candidate_name;
                $performedBy = $record->departmentManager?->name ?? ($record->interviewer?->name ?? 'System');
                $comments = $record->manager_comments ?? ($action === 'approved' ? 'Manager approved' : 'Rejected during review');
                break;
            default:
                $title = 'Workflow #' . $record->id;
                $performedBy = 'System';
                $comments = '';
        }

        return [
            'id' => $record->id,
            'type' => $type,
            'type_label' => $this->labelFor($type),
            'title' => $title,
            'action' => $action,
            'performed_by' => $performedBy,
            'performed_at' => $record->updated_at->format('Y-m-d H:i:s'),
            'comments' => $comments,
        ];
    }

    private function detailsPayload(string $type, $record): array
    {
        $payload = [
            'id' => $record->id,
            'type' => $type,
            'type_label' => $this->labelFor($type),
            'title' => '',
            'description' => '',
            'submitted_by' => 'System',
            'submitted_at' => $record->created_at->format('Y-m-d H:i:s'),
            'priority' => 'medium',
            'status' => $record->status,
            'current_step' => 1,
            'total_steps' => 1,
            'workflow_steps' => [],
            'attachments' => [],
            'comments' => [],
        ];

        switch ($type) {
            case 'employee_registration':
                $payload['title'] = 'Employee Registration - ' . $record->first_name . ' ' . $record->surname;
                $payload['description'] = 'New employee registration requiring final approval';
                $payload['submitted_by'] = $record->creator?->name ?? 'System';
                $payload['priority'] = 'high';
                $payload['current_step'] = $record->status === 'approved' ? 3 : ($record->status === 'rejected' ? 3 : 2);
                $payload['total_steps'] = 3;
                $payload['workflow_steps'] = [
                    ['step' => 1, 'name' => 'Initial Review', 'assignee' => 'System', 'status' => 'completed', 'completed_at' => $record->created_at->format('Y-m-d H:i:s'), 'comments' => 'Initial validation completed'],
                    ['step' => 2, 'name' => 'HR Review', 'assignee' => 'HR Department', 'status' => 'completed', 'completed_at' => $record->created_at->format('Y-m-d H:i:s'), 'comments' => 'HR review completed'],
                    ['step' => 3, 'name' => 'Final Approval', 'assignee' => $record->approver?->name ?? auth()->user()->name, 'status' => $record->status === 'approved' ? 'completed' : ($record->status === 'rejected' ? 'rejected' : 'pending'), 'assigned_at' => $record->created_at->format('Y-m-d H:i:s'), 'comments' => $record->status === 'approved' ? 'Approved' : ($record->ranking_details['rejection_reason'] ?? null)],
                ];
                if ($record->signed_document_path) {
                    $payload['attachments'][] = ['name' => basename($record->signed_document_path), 'path' => $record->signed_document_path, 'uploaded_at' => $record->created_at->format('Y-m-d H:i:s'), 'uploaded_by' => $record->creator?->name ?? 'System'];
                }
                break;

            case 'job_vacancy':
                $payload['title'] = 'Job Vacancy - ' . $record->job_title;
                $payload['description'] = 'New job vacancy requiring approval';
                $payload['submitted_by'] = $record->initiator?->name ?? 'Manager';
                $payload['priority'] = 'medium';
                $payload['total_steps'] = 4;
                $stepMap = ['submitted' => 2, 'supervisor_approved' => 3, 'manager_recommended' => 4, 'hr_approved' => 4];
                $payload['current_step'] = $stepMap[$record->status] ?? 2;
                $payload['workflow_steps'] = [
                    ['step' => 1, 'name' => 'Department Request', 'assignee' => $record->initiator?->name ?? 'Department Head', 'status' => 'completed', 'completed_at' => $record->created_at->format('Y-m-d H:i:s'), 'comments' => 'Department request submitted'],
                    ['step' => 2, 'name' => 'Supervisor Review', 'assignee' => $record->supervisor?->name ?? 'Supervisor', 'status' => $record->supervisor_approved_at ? 'completed' : ($record->status === 'rejected' ? 'rejected' : 'pending'), 'completed_at' => $record->supervisor_approved_at?->format('Y-m-d H:i:s'), 'assigned_at' => $record->created_at->format('Y-m-d H:i:s'), 'comments' => null],
                    ['step' => 3, 'name' => 'Manager Review', 'assignee' => $record->manager?->name ?? 'Manager', 'status' => $record->manager_recommended_at ? 'completed' : ($record->status === 'rejected' ? 'rejected' : 'pending'), 'completed_at' => $record->manager_recommended_at?->format('Y-m-d H:i:s'), 'comments' => null],
                    ['step' => 4, 'name' => 'HR Approval', 'assignee' => $record->hrManager?->name ?? 'HR Manager', 'status' => $record->hr_approved_at ? 'completed' : ($record->status === 'rejected' ? 'rejected' : 'pending'), 'completed_at' => $record->hr_approved_at?->format('Y-m-d H:i:s'), 'comments' => null],
                ];
                foreach ([['shortlisted_file_path', 'shortlisted_candidates.xlsx'], ['signed_file_path', 'signed_approval.pdf']] as [$col, $label]) {
                    if ($record->{$col}) {
                        $payload['attachments'][] = ['name' => $label, 'path' => $record->{$col}, 'uploaded_at' => $record->updated_at->format('Y-m-d H:i:s'), 'uploaded_by' => $record->initiator?->name ?? 'Manager'];
                    }
                }
                break;

            case 'hr_interview':
                $payload['title'] = 'HR Interview - ' . $record->candidate_name;
                $payload['description'] = 'HR interview results requiring review (' . ($record->job_title ?? '') . ')';
                $payload['submitted_by'] = $record->interviewer?->name ?? 'HR';
                $payload['priority'] = 'medium';
                $payload['total_steps'] = 2;
                $payload['current_step'] = in_array($record->status, ['hr_approved', 'rejected']) ? 2 : 1;
                $payload['workflow_steps'] = [
                    ['step' => 1, 'name' => 'Interview Completion', 'assignee' => $record->interviewer?->name ?? 'Interviewer', 'status' => 'completed', 'completed_at' => $record->created_at->format('Y-m-d H:i:s'), 'comments' => 'Interview completed with rating ' . ($record->overall_rating ?? 'N/A')],
                    ['step' => 2, 'name' => 'HR Approval', 'assignee' => $record->hrManager?->name ?? 'HR Manager', 'status' => $record->status === 'hr_approved' ? 'completed' : ($record->status === 'rejected' ? 'rejected' : 'pending'), 'completed_at' => $record->hr_manager_approved_at?->format('Y-m-d H:i:s'), 'comments' => $record->status === 'hr_approved' ? 'Approved' : null],
                ];
                if ($record->signed_file_path) {
                    $payload['attachments'][] = ['name' => 'signed_interview_form.pdf', 'path' => $record->signed_file_path, 'uploaded_at' => $record->updated_at->format('Y-m-d H:i:s'), 'uploaded_by' => $record->interviewer?->name ?? 'HR'];
                }
                break;

            case 'technical_interview':
                $payload['title'] = 'Technical Interview - ' . $record->candidate_name;
                $payload['description'] = 'Technical interview results requiring manager approval (' . ($record->job_title ?? '') . ')';
                $payload['submitted_by'] = $record->interviewer?->name ?? 'Interviewer';
                $payload['priority'] = $record->status === 'interviewer_completed' ? 'high' : 'medium';
                $payload['total_steps'] = 3;
                $payload['current_step'] = match ($record->status) {
                    'manager_approved', 'rejected' => 3,
                    'interviewer_completed' => 2,
                    default => 1,
                };
                $payload['workflow_steps'] = [
                    ['step' => 1, 'name' => 'Technical Assessment', 'assignee' => $record->interviewer?->name ?? 'Interviewer', 'status' => in_array($record->status, ['interviewer_completed', 'manager_approved', 'rejected']) ? 'completed' : 'pending', 'completed_at' => $record->interviewer_completed_at?->format('Y-m-d H:i:s'), 'assigned_at' => $record->created_at->format('Y-m-d H:i:s'), 'comments' => 'Result: ' . $record->getResultLabel()],
                    ['step' => 2, 'name' => 'Manager Approval', 'assignee' => $record->departmentManager?->name ?? 'Department Manager', 'status' => in_array($record->status, ['manager_approved', 'rejected']) ? ($record->status === 'manager_approved' ? 'completed' : 'rejected') : 'pending', 'completed_at' => $record->manager_approved_at?->format('Y-m-d H:i:s'), 'comments' => $record->manager_comments ?? null],
                    ['step' => 3, 'name' => 'HR Confirmation', 'assignee' => 'HR', 'status' => $record->status === 'manager_approved' ? 'completed' : 'waiting', 'comments' => null],
                ];
                if ($record->assessment_report_path) {
                    $payload['attachments'][] = ['name' => 'assessment_report.pdf', 'path' => $record->assessment_report_path, 'uploaded_at' => $record->interviewer_completed_at?->format('Y-m-d H:i:s') ?? $record->created_at->format('Y-m-d H:i:s'), 'uploaded_by' => $record->interviewer?->name ?? 'Interviewer'];
                }
                break;
        }

        // Merge persisted workflow comments with the initial submission note
        $payload['comments'][] = [
            'author' => $payload['submitted_by'],
            'comment' => $payload['type_label'] . ' submitted for approval',
            'created_at' => $payload['submitted_at'],
        ];

        $stored = WorkflowComment::where('workflow_type', $type)
            ->where('workflow_id', $record->id)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($stored as $comment) {
            $payload['comments'][] = [
                'author' => $comment->user?->name ?? 'System',
                'comment' => $comment->comment,
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $payload;
    }

    private function calendarEvent(string $type, $record): array
    {
        $title = $this->labelFor($type) . ' Due - ';
        $days = 3;
        $reviewType = 'deadline';

        switch ($type) {
            case 'employee_registration':
                $title .= $record->first_name . ' ' . $record->surname;
                break;
            case 'job_vacancy':
                $title .= $record->job_title;
                $days = 5;
                $reviewType = 'review';
                break;
            case 'hr_interview':
                $title .= $record->candidate_name;
                $days = 2;
                $reviewType = 'review';
                break;
            case 'technical_interview':
                $title .= $record->candidate_name;
                $days = 2;
                $reviewType = 'review';
                break;
        }

        return [
            'title' => $title,
            'start' => $record->created_at->addDays($days)->format('Y-m-d'),
            'type' => $reviewType,
            'priority' => $this->pendingPayload($type, $record)['priority'],
            'workflow_id' => $record->id,
            'workflow_type' => $type,
        ];
    }

    /**
     * Persist a workflow comment.
     */
    private function storeComment(string $type, $workflowId, string $comment): WorkflowComment
    {
        return WorkflowComment::create([
            'workflow_type' => $type,
            'workflow_id' => $workflowId,
            'client_id' => session('current_client_id'),
            'user_id' => auth()->id(),
            'comment' => $comment,
        ]);
    }

    private function labelFor(string $type): string
    {
        return self::WORKFLOW_TYPES[$type]['label'] ?? ucwords(str_replace('_', ' ', $type));
    }

    private function approvedAtColumn(string $type): string
    {
        return match ($type) {
            'job_vacancy' => 'hr_approved_at',
            'hr_interview' => 'hr_manager_approved_at',
            'technical_interview' => 'manager_approved_at',
            default => 'approved_at',
        };
    }

    private function recordApprovalHours($record, string $column): ?float
    {
        if (!$record->{$column}) {
            return null;
        }
        $diff = $record->created_at->diffInMinutes($record->{$column});
        return round($diff / 60, 1);
    }

    private function averageApprovalHours(string $modelClass, $clientId, string $column): float
    {
        $hours = [];
        $modelClass::where('client_id', $clientId)
            ->whereNotNull($column)
            ->get()
            ->each(function ($record) use (&$hours, $column) {
                $h = $this->recordApprovalHours($record, $column);
                if ($h !== null) {
                    $hours[] = $h;
                }
            });

        return count($hours) > 0 ? round(array_sum($hours) / count($hours), 1) : 0;
    }

    private function approvalStepLabel(string $type): string
    {
        return match ($type) {
            'job_vacancy' => 'HR Approval',
            'hr_interview' => 'HR Review',
            'technical_interview' => 'Manager Approval',
            default => 'Final Approval',
        };
    }
}
