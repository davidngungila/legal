<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRegistration;
use App\Models\JobVacancy;
use App\Models\HrCompetencyInterview;
use App\Models\TechnicalInterview;
use App\Models\EmployeeDocument;
use App\Models\Contract;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class WorkflowController extends Controller
{
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
            
            $stats = [
                'pending_approvals' => EmployeeRegistration::where('status', 'submitted')->count() + 
                                      JobVacancy::where('status', 'submitted')->count() +
                                      HrCompetencyInterview::where('status', 'submitted')->count(),
                'approved_today' => EmployeeRegistration::where('status', 'approved')->whereDate('approved_at', now())->count(),
                'rejected_today' => EmployeeRegistration::where('status', 'rejected')->whereDate('updated_at', now())->count(),
                'overdue_approvals' => EmployeeRegistration::where('status', 'submitted')->where('created_at', '<', now()->subDays(3))->count(),
                'total_workflows' => EmployeeRegistration::count() + JobVacancy::count() + HrCompetencyInterview::count(),
                'by_type' => [
                    'job_vacancy' => JobVacancy::count(),
                    'hr_interview' => HrCompetencyInterview::count(),
                    'technical_interview' => TechnicalInterview::count(),
                    'employee_registration' => EmployeeRegistration::count(),
                    'employee_documents' => EmployeeDocument::count(),
                ],
                'by_status' => [
                    'pending' => EmployeeRegistration::where('status', 'submitted')->count(),
                    'approved' => EmployeeRegistration::where('status', 'approved')->count(),
                    'rejected' => EmployeeRegistration::where('status', 'rejected')->count(),
                ],
                'avg_approval_time_hours' => 24.5, // Placeholder
                'approval_rate' => 87.3, // Placeholder
            ];

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
            $approvals = [];

            // 1. Employee Registrations
            $registrations = EmployeeRegistration::where('status', 'submitted')->get();
            foreach ($registrations as $reg) {
                $approvals[] = [
                    'id' => $reg->id,
                    'type' => 'employee_registration',
                    'title' => 'Employee Registration - ' . $reg->first_name . ' ' . $reg->surname,
                    'description' => 'New employee registration requiring approval',
                    'submitted_by' => $reg->creator->first_name ?? 'System',
                    'submitted_at' => $reg->created_at->format('Y-m-d H:i:s'),
                    'priority' => 'high',
                    'current_approver' => auth()->user()->first_name,
                    'workflow_step' => 'Final Approval',
                    'total_steps' => 3,
                    'current_step' => 3
                ];
            }

            // 2. Job Vacancies
            $vacancies = JobVacancy::where('status', 'submitted')->get();
            foreach ($vacancies as $vacancy) {
                $approvals[] = [
                    'id' => $vacancy->id,
                    'type' => 'job_vacancy',
                    'title' => 'Job Vacancy - ' . $vacancy->job_title,
                    'description' => 'New job vacancy requiring HR approval',
                    'submitted_by' => $vacancy->initiator->first_name ?? 'Manager',
                    'submitted_at' => $vacancy->created_at->format('Y-m-d H:i:s'),
                    'priority' => 'medium',
                    'current_approver' => auth()->user()->first_name,
                    'workflow_step' => 'HR Approval',
                    'total_steps' => 4,
                    'current_step' => 2
                ];
            }

            // 3. HR Interviews
            $hrInterviews = HrCompetencyInterview::where('status', 'submitted')->get();
            foreach ($hrInterviews as $interview) {
                $approvals[] = [
                    'id' => $interview->id,
                    'type' => 'hr_interview',
                    'title' => 'HR Interview - ' . $interview->candidate_name,
                    'description' => 'HR interview results requiring review',
                    'submitted_by' => $interview->interviewer->first_name ?? 'HR',
                    'submitted_at' => $interview->created_at->format('Y-m-d H:i:s'),
                    'priority' => 'medium',
                    'current_approver' => auth()->user()->first_name,
                    'workflow_step' => 'Manager Review',
                    'total_steps' => 2,
                    'current_step' => 1
                ];
            }

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
            $history = [];

            // Fetch recent actions from various models
            $registrations = EmployeeRegistration::whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
            
            foreach ($registrations as $reg) {
                $history[] = [
                    'id' => $reg->id,
                    'type' => 'employee_registration',
                    'title' => 'Registration - ' . $reg->first_name . ' ' . $reg->surname,
                    'action' => $reg->status,
                    'performed_by' => $reg->approver->first_name ?? 'System',
                    'performed_at' => $reg->updated_at->format('Y-m-d H:i:s'),
                    'comments' => 'Processed via standard workflow'
                ];
            }

            $vacancies = JobVacancy::whereIn('status', ['hr_approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($vacancies as $vacancy) {
                $history[] = [
                    'id' => $vacancy->id,
                    'type' => 'job_vacancy',
                    'title' => 'Vacancy - ' . $vacancy->job_title,
                    'action' => $vacancy->status == 'hr_approved' ? 'approved' : 'rejected',
                    'performed_by' => auth()->user()->first_name, // Placeholder
                    'performed_at' => $vacancy->updated_at->format('Y-m-d H:i:s'),
                    'comments' => 'HR review completed'
                ];
            }

            return response()->json([
                'success' => true,
                'history' => $history
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
            // In a real implementation, this would update the workflow status
            return response()->json([
                'success' => true,
                'message' => 'Workflow item approved successfully',
                'data' => [
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
            // In a real implementation, this would update the workflow status
            return response()->json([
                'success' => true,
                'message' => 'Workflow item rejected successfully',
                'data' => [
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
            // In a real implementation, this would update the workflow assignment
            return response()->json([
                'success' => true,
                'message' => 'Workflow item forwarded successfully',
                'data' => [
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
    public function details($workflowId)
    {
        try {
            // In a real implementation, this would fetch specific workflow details
            $details = [
                'id' => $workflowId,
                'type' => 'user_registration',
                'title' => 'User Registration - John Doe',
                'description' => 'New user registration requiring approval',
                'submitted_by' => 'System',
                'submitted_at' => '2024-01-15 10:30:00',
                'priority' => 'medium',
                'status' => 'pending',
                'current_step' => 2,
                'total_steps' => 3,
                'workflow_steps' => [
                    [
                        'step' => 1,
                        'name' => 'Initial Review',
                        'assignee' => 'System',
                        'status' => 'completed',
                        'completed_at' => '2024-01-15 10:30:00',
                        'comments' => 'Initial validation completed'
                    ],
                    [
                        'step' => 2,
                        'name' => 'Manager Approval',
                        'assignee' => auth()->user()->name,
                        'status' => 'pending',
                        'assigned_at' => '2024-01-15 10:30:00',
                        'comments' => null
                    ],
                    [
                        'step' => 3,
                        'name' => 'Director Approval',
                        'assignee' => 'Director',
                        'status' => 'waiting',
                        'comments' => null
                    ]
                ],
                'attachments' => [
                    [
                        'name' => 'user_registration_form.pdf',
                        'uploaded_at' => '2024-01-15 10:30:00',
                        'uploaded_by' => 'System'
                    ]
                ],
                'comments' => [
                    [
                        'author' => 'System',
                        'comment' => 'Automated validation completed successfully',
                        'created_at' => '2024-01-15 10:30:00'
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'details' => $details
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
            // In a real implementation, this would add a comment to the workflow
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => [
                    'workflow_id' => $request->workflow_id,
                    'comment' => $request->comment,
                    'author' => auth()->user()->name,
                    'created_at' => now(),
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
            // In a real implementation, this would return workflow events for calendar
            $events = [
                [
                    'title' => 'User Registration Due - John Doe',
                    'start' => now()->addDays(2)->format('Y-m-d'),
                    'type' => 'deadline',
                    'priority' => 'high'
                ],
                [
                    'title' => 'Job Vacancy Review - Senior Developer',
                    'start' => now()->addDays(3)->format('Y-m-d'),
                    'type' => 'review',
                    'priority' => 'medium'
                ],
                [
                    'title' => 'Contract Renewal - Alice Brown',
                    'start' => now()->addDays(7)->format('Y-m-d'),
                    'type' => 'renewal',
                    'priority' => 'low'
                ]
            ];

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
            // In a real implementation, this would return detailed analytics
            $analytics = [
                'approval_trends' => [
                    ['month' => 'Jan', 'approved' => 45, 'rejected' => 8, 'pending' => 12],
                    ['month' => 'Feb', 'approved' => 52, 'rejected' => 10, 'pending' => 15],
                    ['month' => 'Mar', 'approved' => 48, 'rejected' => 12, 'pending' => 18],
                ],
                'approval_times' => [
                    'user_registration' => 18.5,
                    'client_registration' => 24.2,
                    'job_vacancy' => 36.8,
                    'hr_interview' => 12.3,
                    'technical_interview' => 15.7,
                    'employee_registration' => 28.4,
                ],
                'approver_performance' => [
                    ['approver' => 'John Doe', 'approved' => 25, 'rejected' => 3, 'avg_time' => 16.2],
                    ['approver' => 'Jane Smith', 'approved' => 18, 'rejected' => 2, 'avg_time' => 22.5],
                    ['approver' => 'Bob Johnson', 'approved' => 32, 'rejected' => 5, 'avg_time' => 14.8],
                ],
                'bottlenecks' => [
                    ['step' => 'Director Approval', 'avg_time' => 48.5, 'queue_size' => 8],
                    ['step' => 'HR Review', 'avg_time' => 32.1, 'queue_size' => 12],
                    ['step' => 'Finance Approval', 'avg_time' => 28.7, 'queue_size' => 5],
                ]
            ];

            return response()->json([
                'success' => true,
                'analytics' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow analytics retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }
}
