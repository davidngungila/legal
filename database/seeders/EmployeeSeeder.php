<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Client;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        
        foreach ($clients as $client) {
            // Create sample employees for each client
            $employees = [
                [
                    'client_id' => $client->id,
                    'employee_id' => 'EMP001',
                    'first_name' => 'John',
                    'last_name' => 'Mwangi',
                    'email' => 'john.mwangi+' . $client->id . '@example.com',
                    'phone' => '+255 754 123 001',
                    'gender' => 'male',
                    'date_of_birth' => '1985-06-15',
                    'national_id' => '198506150001',
                    'position' => 'HR Manager',
                    'department' => 'Human Resources',
                    'manager_id' => null,
                    'hire_date' => '2020-01-15',
                    'employment_type' => 'full_time',
                    'status' => 'active',
                    'salary' => 2500000.00,
                    'bank_account' => '0123456789001',
                    'bank_name' => 'CRDB Bank',
                    'address' => 'Kijitonyama, Dar es Salaam',
                    'city' => 'Dar es Salaam',
                    'country' => 'Tanzania',
                    'postal_code' => 'P.O. Box 1234',
                    'emergency_contact_name' => 'Mary Mwangi',
                    'emergency_contact_phone' => '+255 754 123 002',
                ],
                [
                    'client_id' => $client->id,
                    'employee_id' => 'EMP002',
                    'first_name' => 'Grace',
                    'last_name' => 'Kimario',
                    'email' => 'grace.kimario+' . $client->id . '@example.com',
                    'phone' => '+255 754 123 003',
                    'gender' => 'female',
                    'date_of_birth' => '1990-03-22',
                    'national_id' => '199003220002',
                    'position' => 'Finance Officer',
                    'department' => 'Finance',
                    'manager_id' => null,
                    'hire_date' => '2021-03-10',
                    'employment_type' => 'full_time',
                    'status' => 'active',
                    'salary' => 1800000.00,
                    'bank_account' => '0123456789002',
                    'bank_name' => 'NBC Bank',
                    'address' => 'Mikocheni, Dar es Salaam',
                    'city' => 'Dar es Salaam',
                    'country' => 'Tanzania',
                    'postal_code' => 'P.O. Box 5678',
                    'emergency_contact_name' => 'David Kimario',
                    'emergency_contact_phone' => '+255 754 123 004',
                ],
                [
                    'client_id' => $client->id,
                    'employee_id' => 'EMP003',
                    'first_name' => 'Peter',
                    'last_name' => 'Mosha',
                    'email' => 'peter.mosha+' . $client->id . '@example.com',
                    'phone' => '+255 754 123 005',
                    'gender' => 'male',
                    'date_of_birth' => '1988-11-08',
                    'national_id' => '198811080003',
                    'position' => 'Software Developer',
                    'department' => 'IT',
                    'manager_id' => null,
                    'hire_date' => '2022-06-01',
                    'employment_type' => 'full_time',
                    'status' => 'active',
                    'salary' => 2200000.00,
                    'bank_account' => '0123456789003',
                    'bank_name' => 'KCB Bank',
                    'address' => 'Masaki, Dar es Salaam',
                    'city' => 'Dar es Salaam',
                    'country' => 'Tanzania',
                    'postal_code' => 'P.O. Box 9012',
                    'emergency_contact_name' => 'Anna Mosha',
                    'emergency_contact_phone' => '+255 754 123 006',
                ],
                [
                    'client_id' => $client->id,
                    'employee_id' => 'EMP004',
                    'first_name' => 'Sarah',
                    'last_name' => 'Mwalimu',
                    'email' => 'sarah.mwalimu+' . $client->id . '@example.com',
                    'phone' => '+255 754 123 007',
                    'gender' => 'female',
                    'date_of_birth' => '1992-07-14',
                    'national_id' => '199207140004',
                    'position' => 'Marketing Officer',
                    'department' => 'Marketing',
                    'manager_id' => null,
                    'hire_date' => '2023-01-20',
                    'employment_type' => 'full_time',
                    'status' => 'active',
                    'salary' => 1500000.00,
                    'bank_account' => '0123456789004',
                    'bank_name' => 'ACB Bank',
                    'address' => 'Sinza, Dar es Salaam',
                    'city' => 'Dar es Salaam',
                    'country' => 'Tanzania',
                    'postal_code' => 'P.O. Box 3456',
                    'emergency_contact_name' => 'James Mwalimu',
                    'emergency_contact_phone' => '+255 754 123 008',
                ],
                [
                    'client_id' => $client->id,
                    'employee_id' => 'EMP005',
                    'first_name' => 'Michael',
                    'last_name' => 'Kileo',
                    'email' => 'michael.kileo+' . $client->id . '@example.com',
                    'phone' => '+255 754 123 009',
                    'gender' => 'male',
                    'date_of_birth' => '1987-09-03',
                    'national_id' => '198709030005',
                    'position' => 'Operations Manager',
                    'department' => 'Operations',
                    'manager_id' => null,
                    'hire_date' => '2019-11-15',
                    'employment_type' => 'full_time',
                    'status' => 'active',
                    'salary' => 2800000.00,
                    'bank_account' => '0123456789005',
                    'bank_name' => 'DTB Bank',
                    'address' => 'Ubungo, Dar es Salaam',
                    'city' => 'Dar es Salaam',
                    'country' => 'Tanzania',
                    'postal_code' => 'P.O. Box 7890',
                    'emergency_contact_name' => 'Grace Kileo',
                    'emergency_contact_phone' => '+255 754 123 010',
                ],
            ];

            foreach ($employees as $index => $employeeData) {
                // Make employee_id unique per client
                $employeeData['employee_id'] = 'EMP' . str_pad($client->id, 2, '0', STR_PAD_LEFT) . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                
                Employee::firstOrCreate(
                    ['employee_id' => $employeeData['employee_id'], 'client_id' => $client->id],
                    $employeeData
                );
            }
        }

        $this->command->info('Employees seeded successfully for all clients!');
    }
}
