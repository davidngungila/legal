<?php

namespace Database\Seeders;

use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Employee;
use App\Models\LegalCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CaseManagementDemoSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();

        foreach ($clients as $client) {
            $employees = Employee::where('client_id', $client->id)->take(6)->get();
            if ($employees->isEmpty()) {
                continue;
            }

            if (LegalCase::where('client_id', $client->id)->exists()) {
                continue;
            }

            $assignee = User::where('current_client_id', $client->id)->first() ?? User::first();

            $cases = [
                ['type' => 'disciplinary', 'subject' => 'Unauthorized absence', 'priority' => 'high', 'status' => 'under_investigation', 'opened_offset' => 12, 'due_offset' => 3],
                ['type' => 'grievance', 'subject' => 'Working hours dispute', 'priority' => 'medium', 'status' => 'review', 'opened_offset' => 10, 'due_offset' => 5],
                ['type' => 'complaint', 'subject' => 'Harassment allegation', 'priority' => 'high', 'status' => 'documentation', 'opened_offset' => 8, 'due_offset' => 2],
                ['type' => 'legal', 'subject' => 'Contract termination review', 'priority' => 'medium', 'status' => 'resolution', 'opened_offset' => 6, 'due_offset' => 7],
                ['type' => 'disciplinary', 'subject' => 'Policy violation review', 'priority' => 'low', 'status' => 'pending', 'opened_offset' => 4, 'due_offset' => 10],
                ['type' => 'grievance', 'subject' => 'Salary discrepancy follow-up', 'priority' => 'medium', 'status' => 'resolved', 'opened_offset' => 20, 'due_offset' => -2],
            ];

            foreach ($cases as $index => $item) {
                $employee = $employees[$index % $employees->count()];
                $openedDate = Carbon::now()->subDays($item['opened_offset']);
                $dueDate = Carbon::now()->addDays($item['due_offset']);

                $case = LegalCase::create([
                    'client_id' => $client->id,
                    'employee_id' => $employee->id,
                    'case_number' => 'CASE-' . now()->format('Y') . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'case_type' => $item['type'],
                    'subject' => $item['subject'],
                    'description' => 'Demonstration case for ' . $employee->first_name . ' ' . $employee->last_name . ' regarding ' . strtolower($item['subject']) . '.',
                    'opened_date' => $openedDate,
                    'due_date' => $dueDate,
                    'priority' => $item['priority'],
                    'status' => $item['status'],
                    'assigned_to' => $assignee?->id,
                    'created_by' => $assignee?->id,
                    'resolution_notes' => $item['status'] === 'resolved' ? 'Matter reviewed and resolved amicably.' : null,
                    'created_at' => $openedDate,
                    'updated_at' => $openedDate->copy()->addDays(1),
                ]);

                $activities = [
                    ['action' => 'created', 'description' => 'Case file opened and registered.', 'at' => $openedDate],
                    ['action' => 'assigned', 'description' => 'Case assigned to HR/legal officer.', 'at' => $openedDate->copy()->addDay()],
                    ['action' => 'updated', 'description' => 'Initial review notes added to the case.', 'at' => $openedDate->copy()->addDays(2)],
                ];

                if (in_array($item['status'], ['under_investigation', 'documentation', 'resolution'], true)) {
                    $activities[] = ['action' => 'investigation_started', 'description' => 'Investigation and evidence collection started.', 'at' => $openedDate->copy()->addDays(3)];
                }

                if ($item['status'] === 'resolved') {
                    $activities[] = ['action' => 'resolved', 'description' => 'Case resolved and documented.', 'at' => $openedDate->copy()->addDays(5)];
                }

                foreach ($activities as $activity) {
                    CaseActivity::create([
                        'legal_case_id' => $case->id,
                        'user_id' => $assignee?->id,
                        'action' => $activity['action'],
                        'description' => $activity['description'],
                        'created_at' => $activity['at'],
                        'updated_at' => $activity['at'],
                    ]);
                }
            }
        }
    }
}
