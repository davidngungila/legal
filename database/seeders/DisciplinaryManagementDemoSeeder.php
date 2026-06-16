<?php

namespace Database\Seeders;

use App\Models\DisciplinaryAppeal;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryDocument;
use App\Models\DisciplinaryHearing;
use App\Models\DisciplinaryOutcome;
use App\Models\DisciplinaryWarning;
use App\Models\ShowCauseNotice;
use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DisciplinaryManagementDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Disciplinary Management Module...');

        $clients = Client::all();

        foreach ($clients as $client) {
            $employees = Employee::where('client_id', $client->id)->take(4)->get();
            if ($employees->isEmpty()) {
                continue;
            }

            $user = User::where('current_client_id', $client->id)->first() ?? User::first();

            // Minor misconduct case
            $minorCase = DisciplinaryCase::firstOrCreate(
                ['client_id' => $client->id, 'case_number' => 'DISC-' . now()->format('Y') . '-001'],
                [
                    'employee_id' => $employees[0]->id,
                    'case_type' => 'minor',
                    'incident_date' => now()->subDays(10),
                    'incident_description' => 'Unauthorized absence for 1 day.',
                    'reported_by' => $user->id,
                    'status' => 'completed',
                ]
            );

            DisciplinaryWarning::firstOrCreate(
                ['case_id' => $minorCase->id, 'employee_id' => $employees[0]->id],
                [
                    'client_id' => $client->id,
                    'warning_type' => 'first',
                    'issued_date' => now()->subDays(5),
                    'expiry_date' => now()->addMonths(6)->subDays(5),
                    'is_active' => true,
                    'issued_by' => $user->id,
                ]
            );

            // Major misconduct case
            $majorCase = DisciplinaryCase::firstOrCreate(
                ['client_id' => $client->id, 'case_number' => 'DISC-' . now()->format('Y') . '-002'],
                [
                    'employee_id' => $employees[1]->id,
                    'case_type' => 'major',
                    'incident_date' => now()->subDays(20),
                    'incident_description' => 'Violation of company policy regarding data privacy.',
                    'reported_by' => $user->id,
                    'status' => 'hearing_completed',
                ]
            );

            ShowCauseNotice::firstOrCreate(
                ['case_id' => $majorCase->id],
                [
                    'sent_date' => now()->subDays(18),
                    'response_deadline' => now()->subDays(16),
                    'response_received_at' => now()->subDays(17),
                    'response_text' => 'Employee acknowledges the incident and apologizes.',
                    'status' => 'responded',
                ]
            );

            DisciplinaryHearing::firstOrCreate(
                ['case_id' => $majorCase->id],
                [
                    'hearing_date' => now()->subDays(10),
                    'hearing_time' => '10:00:00',
                    'venue' => 'Conference Room A',
                    'notice_sent_at' => now()->subDays(12),
                    'committee_members' => 'HR Manager, Department Head, Legal Officer',
                    'employee_representative' => 'Union Representative',
                    'proceedings_notes' => 'Hearing conducted, all evidence reviewed.',
                ]
            );

            DisciplinaryOutcome::firstOrCreate(
                ['case_id' => $majorCase->id],
                [
                    'outcome_type' => 'final_warning',
                    'outcome_date' => now()->subDays(8),
                    'issued_by' => $user->id,
                    'rationale' => 'Employee has received prior warnings.',
                    'appeal_deadline' => now()->subDays(3),
                ]
            );

            $this->command->info('Disciplinary cases created for client: ' . $client->name);
        }

        $this->command->info('Disciplinary Management demo data created successfully!');
    }
}
