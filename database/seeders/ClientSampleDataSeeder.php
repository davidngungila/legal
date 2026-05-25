<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Employee;
use App\Models\EmployeeRegistration;
use App\Models\JobVacancy;
use App\Models\HrCompetencyInterview;
use App\Models\TechnicalInterview;
use App\Models\LeaveRequest;
use App\Models\PerformanceReview;
use App\Models\SocialRecord;
use App\Models\InductionTraining;
use App\Models\PersonnelIdApplication;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClientSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to truncate tables
        Schema::disableForeignKeyConstraints();
        Employee::truncate();
        EmployeeRegistration::truncate();
        TechnicalInterview::truncate();
        HrCompetencyInterview::truncate();
        JobVacancy::truncate();
        LeaveRequest::truncate();
        PerformanceReview::truncate();
        SocialRecord::truncate();
        InductionTraining::truncate();
        PersonnelIdApplication::truncate();
        Schema::enableForeignKeyConstraints();

        $clients = Client::all();
        $user = User::first(); // Assuming at least one user exists to be the creator

        if (!$user) {
            $user = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $userName = $user->first_name . ' ' . $user->last_name;

        foreach ($clients as $client) {
            // 1. Create 5-10 Employees per client
            $employeeCount = rand(5, 10);
            $employees = [];
            for ($i = 1; $i <= $employeeCount; $i++) {
                $employees[] = Employee::create([
                    'client_id' => $client->id,
                    'employee_id' => 'EMP' . $client->id . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'first_name' => explode(' ', $client->name)[0] . ' Emp',
                    'last_name' => 'Lastname ' . $i,
                    'email' => strtolower(Str::slug($client->name)) . '.c' . $client->id . '.emp' . $i . '@example.com',
                    'phone' => '07' . rand(11111111, 99999999),
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'date_of_birth' => now()->subYears(rand(20, 50)),
                    'national_id' => 'NID' . $client->id . rand(1000000, 9999999),
                    'position' => 'Position ' . $i,
                    'department' => ['HR', 'Finance', 'Operations', 'IT', 'Sales'][rand(0, 4)],
                    'hire_date' => now()->subMonths(rand(1, 60)),
                    'employment_type' => ['full_time', 'part_time', 'contract', 'intern'][rand(0, 3)],
                    'status' => 'active',
                    'salary' => rand(1000000, 5000000),
                    'currency' => 'TZS',
                    'payment_frequency' => 'monthly',
                    'created_by' => $user->id,
                ]);
            }

            // 2. Create 2-3 Job Vacancies per client
            $vacancyCount = rand(2, 3);
            for ($i = 1; $i <= $vacancyCount; $i++) {
                JobVacancy::create([
                    'client_id' => $client->id,
                    'company_name' => $client->name,
                    'job_title' => 'Vacancy ' . $i . ' for ' . $client->name,
                    'vacancy_type' => rand(0, 1) ? 'new_position' : 'replacement',
                    'position_vacant_date' => now()->subDays(rand(1, 30)),
                    'application_date' => now(),
                    'application_deadline' => now()->addDays(14),
                    'department' => 'Operations',
                    'workstation' => 'Main Office',
                    'job_description' => 'Detailed job description for ' . $i,
                    'status' => 'hr_approved',
                    'initiated_by' => $user->id,
                ]);
            }

            // 3. Create Intervews and link to Registrations
            $interviewCount = rand(3, 5);
            for ($i = 1; $i <= $interviewCount; $i++) {
                $hrInterview = HrCompetencyInterview::create([
                    'client_id' => $client->id,
                    'interview_number' => 'HR' . $client->id . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'candidate_name' => 'Candidate ' . $i . ' ' . $client->name,
                    'job_title' => 'Job Title ' . $i,
                    'interview_date' => now()->subDays(rand(5, 10)),
                    'interviewer_name' => $userName,
                    'military_service_status' => 'na',
                    'place_of_recruitment' => 'Main Office',
                    'total_years_experience' => rand(1, 10),
                    'relative_inside_client' => 'no',
                    'birthplace' => 'Dar es Salaam',
                    'residence' => 'Kinondoni',
                    'employed_before' => 'no',
                    'reference_checking' => 'yes',
                    'current_employer_entity' => 'private',
                    'recruiter_recommendation' => 'accepted',
                    'status' => 'hr_approved',
                    'interviewer_id' => $user->id,
                ]);

                $techInterview = TechnicalInterview::create([
                    'client_id' => $client->id,
                    'interview_number' => 'TECH' . $client->id . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'hr_interview_id' => $hrInterview->id,
                    'candidate_name' => 'Candidate ' . $i . ' ' . $client->name,
                    'job_title' => 'Job Title ' . $i,
                    'interview_date' => now()->subDays(rand(1, 4)),
                    'interviewer_name' => $userName,
                    'interviewer_id' => $user->id,
                    'status' => 'manager_approved',
                ]);

                EmployeeRegistration::create([
                    'client_id' => $client->id,
                    'employee_number' => 'REG' . $client->id . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'hr_interview_id' => $hrInterview->id,
                    'technical_interview_id' => $techInterview->id,
                    'first_name' => 'Candidate ' . $i,
                    'surname' => 'Surname ' . $i,
                    'birthplace' => 'Dar es Salaam',
                    'date_of_birth' => now()->subYears(rand(20, 35)),
                    'age' => rand(20, 35),
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'residence_area' => 'Kinondoni',
                    'permanent_residence' => 'Mbezi Beach',
                    'email_address' => 'candidate' . $i . '.c' . $client->id . '@example.com',
                    'phone_number' => '07' . rand(11111111, 99999999),
                    'place_of_recruitment' => 'Main Office',
                    'work_station' => 'Station ' . rand(1, 3),
                    'type_of_contract' => 'Permanent',
                    'job_descriptions' => 'Job descriptions for candidate ' . $i,
                    'date_employed' => now()->subDays(rand(1, 30)),
                    'terms_conditions' => 'Standard terms and conditions',
                    'status' => ['draft', 'submitted', 'approved'][rand(0, 2)],
                    'created_by' => $user->id,
                ]);
            }

            // 4. Create Leave Requests and Performance Reviews for some employees
            foreach (array_slice($employees, 0, 3) as $employee) {
                LeaveRequest::create([
                    'client_id' => $client->id,
                    'employee_id' => $employee->id,
                    'leave_type' => ['annual', 'sick', 'maternity', 'paternity'][rand(0, 3)],
                    'start_date' => now()->addDays(rand(1, 30)),
                    'end_date' => now()->addDays(rand(31, 40)),
                    'days' => rand(1, 10),
                    'reason' => 'Personal reasons',
                    'status' => 'pending',
                ]);

                PerformanceReview::create([
                    'client_id' => $client->id,
                    'employee_id' => $employee->id,
                    'reviewer_id' => $user->id,
                    'review_date' => now()->subMonths(rand(1, 6)),
                    'rating' => rand(3, 5),
                    'comments' => 'Good performance overall.',
                    'status' => 'finalized',
                ]);

                // 5. Create Social Records
                SocialRecord::create([
                    'client_id' => $client->id,
                    'employee_registration_id' => EmployeeRegistration::where('client_id', $client->id)->first()->id ?? 1,
                    'nssf_number' => 'NSSF' . rand(100000, 999999),
                    'nhif_number' => 'NHIF' . rand(100000, 999999),
                    'tin_number' => 'TIN' . rand(100000, 999999),
                    'bank_name' => 'NMB Bank',
                    'bank_account_number' => rand(10000000, 99999999),
                    'emergency_contact_name' => 'Emergency Contact ' . $employee->id,
                    'status' => 'active',
                ]);

                // 6. Create Induction Training
                InductionTraining::create([
                    'client_id' => $client->id,
                    'employee_id' => $employee->id,
                    'training_date' => now()->subDays(rand(10, 20)),
                    'training_type' => 'company_policies',
                    'training_title' => 'Orientation',
                    'training_description' => 'New employee orientation',
                    'trainer_name' => 'HR Manager',
                    'training_duration_hours' => 4.0,
                    'assessment_score' => 90,
                    'assessment_passed' => true,
                    'status' => 'completed',
                ]);

                // 7. Create Personnel ID
                PersonnelIdApplication::create([
                    'client_id' => $client->id,
                    'employee_id' => $employee->id,
                    'id_number' => 'ID' . $client->id . $employee->id . rand(100, 999),
                    'id_type' => 'employee_card',
                    'id_purpose' => 'Identification',
                    'valid_from' => now(),
                    'valid_until' => now()->addYear(),
                    'status' => 'issued',
                ]);
            }
        }
    }
}
