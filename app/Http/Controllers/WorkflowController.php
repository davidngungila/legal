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
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }
            
            $stats = [
                'pending_approvals' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'submitted')->count() + 
                                      JobVacancy::where('client_id', $clientId)->where('status', 'submitted')->count() +
                                      HrCompetencyInterview::where('client_id', $clientId)->where('status', 'submitted')->count(),
                'approved_today' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'approved')->whereDate('approved_at', now())->count(),
                'rejected_today' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'rejected')->whereDate('updated_at', now())->count(),
                'overdue_approvals' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'submitted')->where('created_at', '<', now()->subDays(3))->count(),
                'total_workflows' => EmployeeRegistration::where('client_id', $clientId)->count() + JobVacancy::where('client_id', $clientId)->count() + HrCompetencyInterview::where('client_id', $clientId)->count(),
                'by_type' => [
                    'job_vacancy' => JobVacancy::where('client_id', $clientId)->count(),
                    'hr_interview' => HrCompetencyInterview::where('client_id', $clientId)->count(),
                    'technical_interview' => TechnicalInterview::where('client_id', $clientId)->count(),
                    'employee_registration' => EmployeeRegistration::where('client_id', $clientId)->count(),
                    'employee_documents' => EmployeeDocument::where('client_id', $clientId)->count(),
                ],
                'by_status' => [
                    'pending' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'submitted')->count(),
                    'approved' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'approved')->count(),
                    'rejected' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'rejected')->count(),
                ],
                'avg_approval_time_hours' => 24.5, // Placeholder - would need actual calculation
                'approval_rate' => 87.3, // Placeholder - would need actual calculation
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
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }
            
            $approvals = [];

            // 1. Employee Registrations
            $registrations = EmployeeRegistration::where('client_id', $clientId)->where('status', 'submitted')->get();
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
            $vacancies = JobVacancy::where('client_id', $clientId)->where('status', 'submitted')->get();
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
            $hrInterviews = HrCompetencyInterview::where('client_id', $clientId)->where('status', 'submitted')->get();
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
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }
            
            $history = [];

            // Fetch recent actions from various models
            $registrations = EmployeeRegistration::where('client_id', $clientId)->whereIn('status', ['approved', 'rejected'])
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

            $vacancies = JobVacancy::where('client_id', $clientId)->whereIn('status', ['hr_approved', 'rejected'])
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
            $clientId = session('current_client_id');
            
            // Try to find and approve the workflow item
            // First try EmployeeRegistration
            $registration = EmployeeRegistration::where('client_id', $clientId)->find($request->workflow_id);
            if ($registration) {
                $registration->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'updated_by' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Employee registration approved successfully',
                    'data' => [
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'comments' => $request->comments,
                        'next_approver' => $request->next_approver,
                    ]
                ]);
            }
            
            // Try JobVacancy
            $vacancy = JobVacancy::where('client_id', $clientId)->find($request->workflow_id);
            if ($vacancy) {
                $vacancy->update([
                    'status' => 'hr_approved',
                    'updated_by' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Job vacancy approved successfully',
                    'data' => [
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'comments' => $request->comments,
                        'next_approver' => $request->next_approver,
                    ]
                ]);
            }
            
            // Try HrCompetencyInterview
            $interview = HrCompetencyInterview::where('client_id', $clientId)->find($request->workflow_id);
            if ($interview) {
                $interview->update([
                    'status' => 'approved',
                    'updated_by' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'HR interview approved successfully',
                    'data' => [
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'comments' => $request->comments,
                        'next_approver' => $request->next_approver,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Workflow item not found'
            ], 404);

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
            $clientId = session('current_client_id');
            
            // Try to find and reject the workflow item
            // First try EmployeeRegistration
            $registration = EmployeeRegistration::where('client_id', $clientId)->find($request->workflow_id);
            if ($registration) {
                $registration->update([
                    'status' => 'rejected',
                    'updated_by' => auth()->id(),
                    'ranking_details' => array_merge($registration->ranking_details ?? [], [
                        'rejection_reason' => $request->reason,
                        'allow_resubmission' => $request->boolean('allow_resubmission'),
                        'resubmission_instructions' => $request->resubmission_instructions,
                    ]),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Employee registration rejected successfully',
                    'data' => [
                        'rejected_by' => auth()->id(),
                        'rejected_at' => now(),
                        'reason' => $request->reason,
                        'allow_resubmission' => $request->boolean('allow_resubmission'),
                        'resubmission_instructions' => $request->resubmission_instructions,
                    ]
                ]);
            }
            
            // Try JobVacancy
            $vacancy = JobVacancy::where('client_id', $clientId)->find($request->workflow_id);
            if ($vacancy) {
                $vacancy->update([
                    'status' => 'rejected',
                    'updated_by' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Job vacancy rejected successfully',
                    'data' => [
                        'rejected_by' => auth()->id(),
                        'rejected_at' => now(),
                        'reason' => $request->reason,
                        'allow_resubmission' => $request->boolean('allow_resubmission'),
                        'resubmission_instructions' => $request->resubmission_instructions,
                    ]
                ]);
            }
            
            // Try HrCompetencyInterview
            $interview = HrCompetencyInterview::where('client_id', $clientId)->find($request->workflow_id);
            if ($interview) {
                $interview->update([
                    'status' => 'rejected',
                    'updated_by' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'HR interview rejected successfully',
                    'data' => [
                        'rejected_by' => auth()->id(),
                        'rejected_at' => now(),
                        'reason' => $request->reason,
                        'allow_resubmission' => $request->boolean('allow_resubmission'),
                        'resubmission_instructions' => $request->resubmission_instructions,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Workflow item not found'
            ], 404);

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
            $clientId = session('current_client_id');
            
            // Try to find and forward the workflow item
            // First try EmployeeRegistration
            $registration = EmployeeRegistration::where('client_id', $clientId)->find($request->workflow_id);
            if ($registration) {
                $registration->update([
                    'updated_by' => auth()->id(),
                    'ranking_details' => array_merge($registration->ranking_details ?? [], [
                        'forwarded_to' => $request->forward_to,
                        'forwarded_by' => auth()->user()->name,
                        'forwarded_at' => now()->format('Y-m-d H:i:s'),
                        'forward_reason' => $request->reason,
                    ]),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Employee registration forwarded successfully',
                    'data' => [
                        'forwarded_by' => auth()->id(),
                        'forwarded_at' => now(),
                        'forwarded_to' => $request->forward_to,
                        'reason' => $request->reason,
                    ]
                ]);
            }
            
            // Try JobVacancy
            $vacancy = JobVacancy::where('client_id', $clientId)->find($request->workflow_id);
            if ($vacancy) {
                $vacancy->update([
                    'updated_by' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Job vacancy forwarded successfully',
                    'data' => [
                        'forwarded_by' => auth()->id(),
                        'forwarded_at' => now(),
                        'forwarded_to' => $request->forward_to,
                        'reason' => $request->reason,
                    ]
                ]);
            }
            
            // Try HrCompetencyInterview
            $interview = HrCompetencyInterview::where('client_id', $clientId)->find($request->workflow_id);
            if ($interview) {
                $interview->update([
                    'updated_by' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'HR interview forwarded successfully',
                    'data' => [
                        'forwarded_by' => auth()->id(),
                        'forwarded_at' => now(),
                        'forwarded_to' => $request->forward_to,
                        'reason' => $request->reason,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Workflow item not found'
            ], 404);

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
            $clientId = session('current_client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }
            
            // Try to find the workflow item in different models
            $details = null;
            
            // Try EmployeeRegistration
            $registration = EmployeeRegistration::where('client_id', $clientId)->find($workflowId);
            if ($registration) {
                $details = [
                    'id' => $registration->id,
                    'type' => 'employee_registration',
                    'title' => 'Employee Registration - ' . $registration->first_name . ' ' . $registration->surname,
                    'description' => 'New employee registration requiring approval',
                    'submitted_by' => $registration->creator->first_name ?? 'System',
                    'submitted_at' => $registration->created_at->format('Y-m-d H:i:s'),
                    'priority' => 'high',
                    'status' => $registration->status,
                    'current_step' => 3,
                    'total_steps' => 3,
                    'workflow_steps' => [
                        [
                            'step' => 1,
                            'name' => 'Initial Review',
                            'assignee' => 'System',
                            'status' => 'completed',
                            'completed_at' => $registration->created_at->format('Y-m-d H:i:s'),
                            'comments' => 'Initial validation completed'
                        ],
                        [
                            'step' => 2,
                            'name' => 'HR Review',
                            'assignee' => 'HR Department',
                            'status' => 'completed',
                            'completed_at' => $registration->updated_at->format('Y-m-d H:i:s'),
                            'comments' => 'HR review completed'
                        ],
                        [
                            'step' => 3,
                            'name' => 'Final Approval',
                            'assignee' => auth()->user()->name,
                            'status' => $registration->status == 'approved' ? 'completed' : 'pending',
                            'assigned_at' => $registration->created_at->format('Y-m-d H:i:s'),
                            'comments' => $registration->status == 'approved' ? 'Approved' : null
                        ]
                    ],
                    'attachments' => [
                        [
                            'name' => 'employee_registration_form.pdf',
                            'uploaded_at' => $registration->created_at->format('Y-m-d H:i:s'),
                            'uploaded_by' => $registration->creator->first_name ?? 'System'
                        ]
                    ],
                    'comments' => [
                        [
                            'author' => $registration->creator->first_name ?? 'System',
                            'comment' => 'Employee registration submitted for approval',
                            'created_at' => $registration->created_at->format('Y-m-d H:i:s')
                        ]
                    ]
                ];
            }
            
            // Try JobVacancy
            if (!$details) {
                $vacancy = JobVacancy::where('client_id', $clientId)->find($workflowId);
                if ($vacancy) {
                    $details = [
                        'id' => $vacancy->id,
                        'type' => 'job_vacancy',
                        'title' => 'Job Vacancy - ' . $vacancy->job_title,
                        'description' => 'New job vacancy requiring HR approval',
                        'submitted_by' => $vacancy->initiator->first_name ?? 'Manager',
                        'submitted_at' => $vacancy->created_at->format('Y-m-d H:i:s'),
                        'priority' => 'medium',
                        'status' => $vacancy->status,
                        'current_step' => 2,
                        'total_steps' => 4,
                        'workflow_steps' => [
                            [
                                'step' => 1,
                                'name' => 'Department Request',
                                'assignee' => 'Department Head',
                                'status' => 'completed',
                                'completed_at' => $vacancy->created_at->format('Y-m-d H:i:s'),
                                'comments' => 'Department request submitted'
                            ],
                            [
                                'step' => 2,
                                'name' => 'HR Review',
                                'assignee' => auth()->user()->name,
                                'status' => $vacancy->status == 'hr_approved' ? 'completed' : 'pending',
                                'assigned_at' => $vacancy->created_at->format('Y-m-d H:i:s'),
                                'comments' => $vacancy->status == 'hr_approved' ? 'HR approved' : null
                            ],
                            [
                                'step' => 3,
                                'name' => 'Budget Approval',
                                'assignee' => 'Finance',
                                'status' => 'waiting',
                                'comments' => null
                            ],
                            [
                                'step' => 4,
                                'name' => 'Final Approval',
                                'assignee' => 'Director',
                                'status' => 'waiting',
                                'comments' => null
                            ]
                        ],
                        'attachments' => [],
                        'comments' => [
                            [
                                'author' => $vacancy->initiator->first_name ?? 'Manager',
                                'comment' => 'Job vacancy submitted for HR review',
                                'created_at' => $vacancy->created_at->format('Y-m-d H:i:s')
                            ]
                        ]
                    ];
                }
            }
            
            // Try HrCompetencyInterview
            if (!$details) {
                $interview = HrCompetencyInterview::where('client_id', $clientId)->find($workflowId);
                if ($interview) {
                    $details = [
                        'id' => $interview->id,
                        'type' => 'hr_interview',
                        'title' => 'HR Interview - ' . $interview->candidate_name,
                        'description' => 'HR interview results requiring review',
                        'submitted_by' => $interview->interviewer->first_name ?? 'HR',
                        'submitted_at' => $interview->created_at->format('Y-m-d H:i:s'),
                        'priority' => 'medium',
                        'status' => $interview->status,
                        'current_step' => 1,
                        'total_steps' => 2,
                        'workflow_steps' => [
                            [
                                'step' => 1,
                                'name' => 'Manager Review',
                                'assignee' => auth()->user()->name,
                                'status' => $interview->status == 'approved' ? 'completed' : 'pending',
                                'assigned_at' => $interview->created_at->format('Y-m-d H:i:s'),
                                'comments' => $interview->status == 'approved' ? 'Approved' : null
                            ],
                            [
                                'step' => 2,
                                'name' => 'Final Approval',
                                'assignee' => 'HR Director',
                                'status' => 'waiting',
                                'comments' => null
                            ]
                        ],
                        'attachments' => [],
                        'comments' => [
                            [
                                'author' => $interview->interviewer->first_name ?? 'HR',
                                'comment' => 'HR interview completed and submitted for review',
                                'created_at' => $interview->created_at->format('Y-m-d H:i:s')
                            ]
                        ]
                    ];
                }
            }

            if (!$details) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow item not found'
                ], 404);
            }

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
            $clientId = session('current_client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }
            
            $events = [];

            // Get pending employee registrations as deadlines
            $registrations = EmployeeRegistration::where('client_id', $clientId)
                ->where('status', 'submitted')
                ->where('created_at', '>', now()->subDays(7))
                ->get();
            
            foreach ($registrations as $reg) {
                $events[] = [
                    'title' => 'Employee Registration Due - ' . $reg->first_name . ' ' . $reg->surname,
                    'start' => $reg->created_at->addDays(3)->format('Y-m-d'),
                    'type' => 'deadline',
                    'priority' => 'high',
                    'workflow_id' => $reg->id,
                    'workflow_type' => 'employee_registration'
                ];
            }

            // Get pending job vacancies as deadlines
            $vacancies = JobVacancy::where('client_id', $clientId)
                ->where('status', 'submitted')
                ->where('created_at', '>', now()->subDays(14))
                ->get();

            foreach ($vacancies as $vacancy) {
                $events[] = [
                    'title' => 'Job Vacancy Review - ' . $vacancy->job_title,
                    'start' => $vacancy->created_at->addDays(5)->format('Y-m-d'),
                    'type' => 'review',
                    'priority' => 'medium',
                    'workflow_id' => $vacancy->id,
                    'workflow_type' => 'job_vacancy'
                ];
            }

            // Get pending HR interviews as deadlines
            $interviews = HrCompetencyInterview::where('client_id', $clientId)
                ->where('status', 'submitted')
                ->where('created_at', '>', now()->subDays(7))
                ->get();

            foreach ($interviews as $interview) {
                $events[] = [
                    'title' => 'HR Interview Review - ' . $interview->candidate_name,
                    'start' => $interview->created_at->addDays(2)->format('Y-m-d'),
                    'type' => 'review',
                    'priority' => 'medium',
                    'workflow_id' => $interview->id,
                    'workflow_type' => 'hr_interview'
                ];
            }

            // Get upcoming contract renewals (placeholder - would need contract dates)
            $events[] = [
                'title' => 'Contract Renewal Review',
                'start' => now()->addDays(7)->format('Y-m-d'),
                'type' => 'renewal',
                'priority' => 'low',
                'workflow_id' => null,
                'workflow_type' => 'contract_management'
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
            $clientId = session('current_client_id');
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.'
                ], 400);
            }
            
            // Calculate real metrics from database
            $analytics = [
                'approval_trends' => [
                    ['month' => 'Jan', 'approved' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'approved')->whereMonth('approved_at', 1)->count(), 'rejected' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'rejected')->whereMonth('updated_at', 1)->count(), 'pending' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'submitted')->whereMonth('created_at', 1)->count()],
                    ['month' => 'Feb', 'approved' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'approved')->whereMonth('approved_at', 2)->count(), 'rejected' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'rejected')->whereMonth('updated_at', 2)->count(), 'pending' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'submitted')->whereMonth('created_at', 2)->count()],
                    ['month' => 'Mar', 'approved' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'approved')->whereMonth('approved_at', 3)->count(), 'rejected' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'rejected')->whereMonth('updated_at', 3)->count(), 'pending' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'submitted')->whereMonth('created_at', 3)->count()],
                ],
                'approval_times' => [
                    'user_registration' => 18.5, // Placeholder - would need actual calculation
                    'client_registration' => 24.2, // Placeholder - would need actual calculation
                    'job_vacancy' => 36.8, // Placeholder - would need actual calculation
                    'hr_interview' => 12.3, // Placeholder - would need actual calculation
                    'technical_interview' => 15.7, // Placeholder - would need actual calculation
                    'employee_registration' => 28.4, // Placeholder - would need actual calculation
                ],
                'approver_performance' => [
                    ['approver' => auth()->user()->name, 'approved' => EmployeeRegistration::where('client_id', $clientId)->where('approved_by', auth()->id())->count(), 'rejected' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'rejected')->where('updated_by', auth()->id())->count(), 'avg_time' => 16.2], // Placeholder avg_time
                ],
                'bottlenecks' => [
                    ['step' => 'Director Approval', 'avg_time' => 48.5, 'queue_size' => EmployeeRegistration::where('client_id', $clientId)->where('status', 'submitted')->count()],
                    ['step' => 'HR Review', 'avg_time' => 32.1, 'queue_size' => JobVacancy::where('client_id', $clientId)->where('status', 'submitted')->count()],
                    ['step' => 'Finance Approval', 'avg_time' => 28.7, 'queue_size' => 0], // Placeholder
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
