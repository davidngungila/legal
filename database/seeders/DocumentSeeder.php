<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Client;
use Illuminate\Support\Str;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        
        foreach ($clients as $client) {
            // Sample documents for each client
            $documents = [
                [
                    'title' => 'Permanent Employment Agreement',
                    'description' => 'Standard employment contract for permanent staff members in accordance with Tanzania Labour Act.',
                    'document_type' => 'contract',
                    'file_path' => 'documents/contracts/permanent_employment_agreement.pdf',
                    'file_size' => 245760,
                    'file_type' => 'pdf',
                    'status' => 'active',
                    'version' => '2.1',
                    'effective_date' => now()->subMonths(12),
                    'expiry_date' => null,
                    'is_required' => true,
                    'is_public' => true,
                    'category' => 'HR',
                    'tags' => ['employment', 'contract', 'permanent', 'legal'],
                ],
                [
                    'title' => 'Employee Handbook',
                    'description' => 'Comprehensive guide to company policies, procedures, and workplace expectations.',
                    'document_type' => 'handbook',
                    'file_path' => 'documents/handbooks/employee_handbook.pdf',
                    'file_size' => 512000,
                    'file_type' => 'pdf',
                    'status' => 'active',
                    'version' => '3.0',
                    'effective_date' => now()->subMonths(6),
                    'expiry_date' => null,
                    'is_required' => false,
                    'is_public' => true,
                    'category' => 'HR',
                    'tags' => ['handbook', 'policies', 'procedures', 'guidelines'],
                ],
                [
                    'title' => 'Code of Conduct',
                    'description' => 'Ethical guidelines and professional standards for all employees.',
                    'document_type' => 'policy',
                    'file_path' => 'documents/policies/code_of_conduct.pdf',
                    'file_size' => 163840,
                    'file_type' => 'pdf',
                    'status' => 'active',
                    'version' => '1.5',
                    'effective_date' => now()->subMonths(8),
                    'expiry_date' => null,
                    'is_required' => true,
                    'is_public' => true,
                    'category' => 'HR',
                    'tags' => ['ethics', 'conduct', 'professional', 'standards'],
                ],
                [
                    'title' => 'Workplace Safety Policy',
                    'description' => 'Comprehensive safety guidelines and emergency procedures for workplace safety.',
                    'document_type' => 'safety',
                    'file_path' => 'documents/safety/workplace_safety_policy.pdf',
                    'file_size' => 294912,
                    'file_type' => 'pdf',
                    'status' => 'active',
                    'version' => '2.0',
                    'effective_date' => now()->subMonths(10),
                    'expiry_date' => null,
                    'is_required' => true,
                    'is_public' => true,
                    'category' => 'Safety',
                    'tags' => ['safety', 'emergency', 'health', 'procedures'],
                ],
                [
                    'title' => 'Leave Policy',
                    'description' => 'Detailed leave entitlements and procedures for all types of leave.',
                    'document_type' => 'policy',
                    'file_path' => 'documents/policies/leave_policy.pdf',
                    'file_size' => 131072,
                    'file_type' => 'pdf',
                    'status' => 'active',
                    'version' => '1.2',
                    'effective_date' => now()->subMonths(4),
                    'expiry_date' => null,
                    'is_required' => false,
                    'is_public' => true,
                    'category' => 'HR',
                    'tags' => ['leave', 'absence', 'policy', 'entitlement'],
                ],
                [
                    'title' => 'Remote Work Policy',
                    'description' => 'Guidelines and procedures for remote work arrangements.',
                    'document_type' => 'policy',
                    'file_path' => 'documents/policies/remote_work_policy.pdf',
                    'file_size' => 98304,
                    'file_type' => 'pdf',
                    'status' => 'active',
                    'version' => '1.0',
                    'effective_date' => now()->subMonths(2),
                    'expiry_date' => now()->addMonths(12),
                    'is_required' => false,
                    'is_public' => true,
                    'category' => 'HR',
                    'tags' => ['remote', 'work', 'policy', 'flexibility'],
                ],
                [
                    'title' => 'IT Security Policy',
                    'description' => 'Information security guidelines and procedures for all employees.',
                    'document_type' => 'policy',
                    'file_path' => 'documents/policies/it_security_policy.pdf',
                    'file_size' => 196608,
                    'file_type' => 'pdf',
                    'status' => 'active',
                    'version' => '2.1',
                    'effective_date' => now()->subMonths(3),
                    'expiry_date' => null,
                    'is_required' => true,
                    'is_public' => true,
                    'category' => 'IT',
                    'tags' => ['security', 'IT', 'data', 'protection'],
                ],
                [
                    'title' => 'Performance Review Form',
                    'description' => 'Standard form for employee performance evaluations.',
                    'document_type' => 'form',
                    'file_path' => 'documents/forms/performance_review_form.docx',
                    'file_size' => 65536,
                    'file_type' => 'docx',
                    'status' => 'active',
                    'version' => '3.2',
                    'effective_date' => now()->subMonths(5),
                    'expiry_date' => null,
                    'is_required' => false,
                    'is_public' => true,
                    'category' => 'HR',
                    'tags' => ['performance', 'review', 'evaluation', 'form'],
                ],
            ];
            
            foreach ($documents as $docData) {
                Document::firstOrCreate(
                    [
                        'client_id' => $client->id,
                        'title' => $docData['title'],
                        'document_type' => $docData['document_type'],
                    ],
                    array_merge($docData, [
                        'client_id' => $client->id,
                        'created_by' => 1, // Assuming user ID 1 exists
                        'updated_by' => 1,
                    ])
                );
            }
        }

        $this->command->info('Documents seeded successfully!');
    }
}
