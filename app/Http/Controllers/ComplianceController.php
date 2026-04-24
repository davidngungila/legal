<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ComplianceController extends Controller
{
    /**
     * Display the compliance dashboard.
     */
    public function index()
    {
        $clientId = session('current_client_id');
        
        if (!$clientId) {
            // Get first available client as default
            $firstClient = Client::orderBy('created_at', 'desc')->first();
            if ($firstClient) {
                session(['current_client_id' => $firstClient->id, 'current_client_name' => $firstClient->name]);
                $clientId = $firstClient->id;
            }
        }

        if (!$clientId) {
            return redirect()->route('clients.index')
                ->with('error', 'No clients available. Please create a client first.');
        }

        $client = Client::find($clientId);
        
        if (!$client) {
            return redirect()->route('clients.index')
                ->with('error', 'Client not found.');
        }

        // Calculate compliance scores
        $complianceData = $this->calculateComplianceData($client);
        
        return view('compliance.index', compact('client', 'complianceData'));
    }

    /**
     * Run compliance audit.
     */
    public function runAudit(Request $request)
    {
        try {
            $clientId = session('current_client_id');
            
            if (!$clientId) {
                // Try to get first available client
                $firstClient = Client::orderBy('created_at', 'desc')->first();
                if ($firstClient) {
                    session(['current_client_id' => $firstClient->id, 'current_client_name' => $firstClient->name]);
                    $clientId = $firstClient->id;
                } else {
                    return response()->json(['error' => 'No clients available. Please create a client first.'], 400);
                }
            }

            $client = Client::find($clientId);
            
            if (!$client) {
                return response()->json(['error' => 'Client not found.'], 404);
            }

            // Simulate audit process
            $auditResults = $this->performComplianceAudit($client);

            return response()->json([
                'success' => true,
                'message' => 'Compliance audit completed successfully!',
                'results' => $auditResults
            ]);
        } catch (\Exception $e) {
            \Log::error('Compliance audit error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to run audit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get compliance reports.
     */
    public function getReports(Request $request)
    {
        try {
            $clientId = session('current_client_id');
            
            if (!$clientId) {
                // Try to get first available client
                $firstClient = Client::orderBy('created_at', 'desc')->first();
                if ($firstClient) {
                    session(['current_client_id' => $firstClient->id, 'current_client_name' => $firstClient->name]);
                    $clientId = $firstClient->id;
                } else {
                    return response()->json(['error' => 'No clients available. Please create a client first.'], 400);
                }
            }

            $client = Client::find($clientId);
            
            if (!$client) {
                return response()->json(['error' => 'Client not found.'], 404);
            }

            $reports = $this->generateComplianceReports($client);

            return response()->json([
                'success' => true,
                'reports' => $reports
            ]);
        } catch (\Exception $e) {
            \Log::error('Compliance reports error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate reports: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download compliance report.
     */
    public function downloadReport(Request $request)
    {
        try {
            $reportType = $request->get('type', 'summary');
            $clientId = session('current_client_id');
            
            if (!$clientId) {
                // Try to get first available client
                $firstClient = Client::orderBy('created_at', 'desc')->first();
                if ($firstClient) {
                    session(['current_client_id' => $firstClient->id, 'current_client_name' => $firstClient->name]);
                    $clientId = $firstClient->id;
                } else {
                    return response()->json(['error' => 'No clients available. Please create a client first.'], 400);
                }
            }

            $client = Client::find($clientId);
            
            if (!$client) {
                return response()->json(['error' => 'Client not found.'], 404);
            }

            // Generate report (simplified for demo)
            $reportData = $this->generateComplianceReports($client);
            
            return response()->json([
                'success' => true,
                'message' => 'Report downloaded successfully',
                'data' => $reportData[$reportType] ?? $reportData['summary']
            ]);
        } catch (\Exception $e) {
            \Log::error('Compliance download error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to download report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate compliance data for dashboard.
     */
    private function calculateComplianceData($client)
    {
        $overallScore = $this->calculateOverallComplianceScore($client);
        $riskLevel = $this->assessRiskLevel($overallScore);
        $lastAudit = $this->getLastAuditDate($client);
        $nextAudit = $this->getNextAuditDate($lastAudit);

        return [
            'overall_score' => $overallScore,
            'risk_level' => $riskLevel,
            'last_audit' => $lastAudit,
            'next_audit' => $nextAudit,
            'compliance_areas' => $this->getComplianceAreas($client),
            'recent_audits' => $this->getRecentAudits($client),
            'upcoming_deadlines' => $this->getUpcomingDeadlines($client)
        ];
    }

    /**
     * Calculate overall compliance score.
     */
    private function calculateOverallComplianceScore($client)
    {
        $score = 100;
        
        // Check legal requirements
        if (!$client->registration_number) $score -= 15;
        if (!$client->tin_number) $score -= 10;
        if (!$client->nssf_employer_number) $score -= 10;
        if (!$client->wcf_employer_number) $score -= 10;
        
        // Check employee compliance
        $employeeCount = $client->employees()->count();
        if ($employeeCount > 0) {
            $compliantEmployees = $client->employees()->where('status', 'active')->count();
            $employeeComplianceRate = ($compliantEmployees / $employeeCount) * 100;
            $score -= (100 - $employeeComplianceRate) * 0.2;
        }
        
        // Check documentation
        if (!$client->address) $score -= 5;
        if (!$client->phone) $score -= 5;
        if (!$client->email) $score -= 5;
        
        return max(0, min(100, round($score)));
    }

    /**
     * Assess risk level based on compliance score.
     */
    private function assessRiskLevel($score)
    {
        if ($score >= 90) return 'LOW';
        if ($score >= 70) return 'MEDIUM';
        return 'HIGH';
    }

    /**
     * Get last audit date.
     */
    private function getLastAuditDate($client)
    {
        // Simulate last audit date (in real app, this would come from database)
        return Carbon::now()->subDays(15);
    }

    /**
     * Get next audit date.
     */
    private function getNextAuditDate($lastAudit)
    {
        return $lastAudit->copy()->addDays(30);
    }

    /**
     * Get compliance areas with scores.
     */
    private function getComplianceAreas($client)
    {
        return [
            [
                'name' => 'Labour Law Compliance',
                'score' => 95,
                'status' => 'compliant',
                'description' => 'Minimum wage, working hours, leave policies'
            ],
            [
                'name' => 'Tax Compliance',
                'score' => 92,
                'status' => 'compliant',
                'description' => 'PAYE, withholding tax, VAT compliance'
            ],
            [
                'name' => 'Social Security',
                'score' => 88,
                'status' => 'review',
                'description' => 'NSSF, NHIF contributions and reporting'
            ],
            [
                'name' => 'Health & Safety',
                'score' => 96,
                'status' => 'compliant',
                'description' => 'Workplace safety standards and protocols'
            ]
        ];
    }

    /**
     * Get recent audit history.
     */
    private function getRecentAudits($client)
    {
        return [
            [
                'date' => Carbon::now()->subDays(15),
                'type' => 'Internal Compliance Audit',
                'auditor' => 'Internal Compliance Team',
                'score' => 94,
                'status' => 'passed'
            ],
            [
                'date' => Carbon::now()->subMonths(2),
                'type' => 'Labour Law Inspection',
                'auditor' => 'Ministry of Labour',
                'score' => 91,
                'status' => 'passed'
            ],
            [
                'date' => Carbon::now()->subMonths(6),
                'type' => 'Data Protection Audit',
                'auditor' => 'External Consultant',
                'score' => 85,
                'status' => 'improvements_needed'
            ]
        ];
    }

    /**
     * Get upcoming compliance deadlines.
     */
    private function getUpcomingDeadlines($client)
    {
        return [
            [
                'title' => 'Annual Tax Return Filing',
                'due_date' => Carbon::now()->addDays(30),
                'priority' => 'high'
            ],
            [
                'title' => 'NSSF Quarterly Report',
                'due_date' => Carbon::now()->addDays(45),
                'priority' => 'medium'
            ],
            [
                'title' => 'Workplace Safety Inspection',
                'due_date' => Carbon::now()->addDays(60),
                'priority' => 'medium'
            ]
        ];
    }

    /**
     * Perform compliance audit.
     */
    private function performComplianceAudit($client)
    {
        // Simulate comprehensive audit process
        $areas = $this->getComplianceAreas($client);
        $overallScore = $this->calculateOverallComplianceScore($client);
        
        return [
            'audit_id' => 'AUD-' . uniqid(),
            'date' => Carbon::now(),
            'overall_score' => $overallScore,
            'risk_level' => $this->assessRiskLevel($overallScore),
            'areas_assessed' => $areas,
            'recommendations' => $this->generateRecommendations($areas, $overallScore),
            'next_review_date' => Carbon::now()->addMonths(3)
        ];
    }

    /**
     * Generate compliance reports.
     */
    private function generateComplianceReports($client)
    {
        return [
            'summary' => [
                'title' => 'Compliance Summary Report',
                'generated_at' => Carbon::now(),
                'overall_score' => $this->calculateOverallComplianceScore($client),
                'risk_level' => $this->assessRiskLevel($this->calculateOverallComplianceScore($client))
            ],
            'detailed' => [
                'title' => 'Detailed Compliance Report',
                'generated_at' => Carbon::now(),
                'areas' => $this->getComplianceAreas($client),
                'recommendations' => $this->generateRecommendations($this->getComplianceAreas($client), $this->calculateOverallComplianceScore($client))
            ],
            'audit_history' => [
                'title' => 'Audit History Report',
                'generated_at' => Carbon::now(),
                'audits' => $this->getRecentAudits($client)
            ]
        ];
    }

    /**
     * Generate recommendations based on audit results.
     */
    private function generateRecommendations($areas, $overallScore)
    {
        $recommendations = [];
        
        foreach ($areas as $area) {
            if ($area['score'] < 90) {
                $recommendations[] = [
                    'area' => $area['name'],
                    'priority' => $area['score'] < 80 ? 'high' : 'medium',
                    'action' => "Improve {$area['name']} compliance score from {$area['score']}% to at least 90%"
                ];
            }
        }
        
        if ($overallScore < 85) {
            $recommendations[] = [
                'area' => 'Overall Compliance',
                'priority' => 'high',
                'action' => 'Schedule comprehensive compliance review with legal counsel'
            ];
        }
        
        return $recommendations;
    }
}
