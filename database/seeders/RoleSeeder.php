<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            // ROLE 1: Super Admin (HR-ADMIN Firm)
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'HR-Admin Firm - Full system access with all permissions across all modules',
                'permissions' => [
                    // Full system access
                    'system.manage', 'system.monitoring', 'system.backup', 'system.maintenance', 'system.integration',
                    'audit.view', 'audit.trail', 'audit.compliance', 'audit.export',
                    'security.manage', 'security.encryption', 'security.audit', 'security.access', 'security.consent', 'security.backup', 'security.roles', 'security.masking', 'security.activity', 'security.mfa', 'security.incident',
                    'notifications.manage', 'notifications.alerts', 'notifications.automated',
                    
                    // Organization & Workforce
                    'organization.view', 'organization.setup', 'organization.sector', 'organization.structure', 'organization.employment', 'organization.union',
                    
                    // Employee Master Data
                    'employees.view', 'employees.create', 'employees.edit', 'employees.delete', 'employees.contract', 'employees.workpermit', 'employees.medical', 'employees.discipline', 'employees.performance', 'employees.promotion', 'employees.training', 'employees.loan', 'employees.nextofkin', 'employees.probation', 'employees.signature',
                    
                    // Recruitment
                    'recruitment.view', 'recruitment.requisition', 'recruitment.approval', 'recruitment.advert', 'recruitment.candidates', 'recruitment.budget', 'recruitment.interview', 'recruitment.background', 'recruitment.offer', 'recruitment.risk', 'recruitment.reference', 'recruitment.diversity', 'recruitment.ai', 'recruitment.onboarding',
                    
                    // Onboarding
                    'onboarding.view', 'onboarding.checklist', 'onboarding.policy', 'onboarding.osha', 'onboarding.contract', 'onboarding.template', 'onboarding.probation', 'onboarding.signing', 'onboarding.approval',
                    
                    // Attendance & Timesheet
                    'attendance.view', 'attendance.manage', 'attendance.biometric', 'attendance.shift', 'attendance.overtime', 'attendance.absence', 'attendance.leave', 'attendance.alert', 'attendance.productivity', 'attendance.legal',
                    
                    // Payroll Management
                    'payroll.view', 'payroll.manage', 'payroll.grossnet', 'payroll.paye', 'payroll.nssf', 'payroll.wcf', 'payroll.heslb', 'payroll.deductions', 'payroll.payslip', 'payroll.audit', 'payroll.overtime', 'payroll.holiday', 'payroll.leavepay', 'payroll.final', 'payroll.severance', 'payroll.termination', 'payroll.compliance',
                    
                    // Compensation & Benefits
                    'compensation.view', 'compensation.manage', 'compensation.salary', 'compensation.allowance', 'compensation.benefits', 'compensation.tax', 'compensation.equity', 'compensation.eligibility', 'compensation.medical', 'compensation.pension', 'compensation.loan', 'compensation.incentive', 'compensation.projection',
                    
                    // Performance Management
                    'performance.view', 'performance.manage', 'performance.goals', 'performance.appraisal', 'performance.pip', 'performance.warning', 'performance.kpi', 'performance.okr', 'performance.360', 'performance.improvement', 'performance.discipline', 'performance.promotion', 'performance.talent', 'performance.documentation',
                    
                    // Employee Relations & Discipline (CRITICAL)
                    'discipline.view', 'discipline.manage', 'discipline.incident', 'discipline.investigation', 'discipline.letter', 'discipline.report', 'discipline.misconduct', 'discipline.progressive', 'discipline.evidence', 'discipline.hearing', 'discipline.adjourn', 'discipline.approval', 'discipline.risk', 'discipline.case', 'discipline.timeline', 'discipline.witness', 'discipline.sensitivity', 'discipline.engine', 'discipline.legal', 'discipline.precedent', 'discipline.score', 'discipline.exposure', 'discipline.block', 'discipline.suspension', 'discipline.warning', 'discipline.termination', 'discipline.outcome',
                    
                    // Compliance & Legal
                    'compliance.view', 'compliance.manage', 'compliance.elra', 'compliance.labour', 'compliance.osha', 'compliance.union', 'compliance.data', 'compliance.database', 'compliance.audit', 'compliance.scan', 'compliance.filing', 'compliance.update', 'compliance.expat', 'compliance.policy', 'compliance.bargaining', 'compliance.dashboard',
                    
                    // Training & Development
                    'training.view', 'training.manage', 'training.needs', 'training.compliance', 'training.records', 'training.skills', 'training.certification', 'training.budget', 'training.history', 'training.compliance', 'training.succession',
                    
                    // Talent Management
                    'talent.view', 'talent.manage', 'talent.succession', 'talent.potential', 'talent.skills',
                    
                    // Workforce Analytics
                    'analytics.view', 'analytics.manage', 'analytics.dashboard', 'analytics.turnover', 'analytics.absenteeism', 'analytics.disciplinary', 'analytics.diversity', 'analytics.payroll', 'analytics.overtime', 'analytics.legal', 'analytics.dispute', 'analytics.risk', 'analytics.satisfaction', 'analytics.predictive',
                    
                    // Employee Self Service
                    'selfservice.view', 'selfservice.profile', 'selfservice.leave', 'selfservice.balance', 'selfservice.training', 'selfservice.payslip', 'selfservice.personal', 'selfservice.contract', 'selfservice.complaint', 'selfservice.performance', 'selfservice.loan', 'selfservice.policy', 'selfservice.tracking',
                    
                    // Case Management
                    'casemanagement.view', 'casemanagement.manage', 'casemanagement.generator', 'casemanagement.chronology', 'casemanagement.evidence', 'casemanagement.witness', 'casemanagement.readiness',
                    
                    // Dashboard & Reports
                    'dashboard.view', 'reports.view', 'reports.create', 'reports.export', 'reports.schedule',
                    
                    // Client Management (Multi-tenant)
                    'clients.view', 'clients.create', 'clients.edit', 'clients.delete', 'clients.tenant', 'clients.data',
                    
                    // User & Role Management
                    'users.view', 'users.create', 'users.edit', 'users.delete', 'users.roles', 'users.permissions', 'users.audit',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'roles.permissions', 'roles.audit',
                    'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete', 'permissions.audit',
                ],
            ],
            
            // ROLE 2: Lead HR HR-Admin
            [
                'name' => 'lead_hr_admin',
                'display_name' => 'Lead HR Admin',
                'description' => 'Lead HR Admin - Strategic HR management with oversight responsibilities',
                'permissions' => [
                    // System access (limited)
                    'system.view', 'system.monitoring',
                    'audit.view', 'audit.trail',
                    'security.view', 'security.audit', 'security.access',
                    'notifications.view', 'notifications.manage',
                    
                    // Organization & Workforce
                    'organization.view', 'organization.setup', 'organization.sector', 'organization.structure', 'organization.employment', 'organization.union',
                    
                    // Employee Master Data
                    'employees.view', 'employees.create', 'employees.edit', 'employees.contract', 'employees.workpermit', 'employees.discipline', 'employees.performance', 'employees.promotion', 'employees.training', 'employees.loan', 'employees.nextofkin', 'employees.probation', 'employees.signature',
                    
                    // Recruitment
                    'recruitment.view', 'recruitment.requisition', 'recruitment.approval', 'recruitment.advert', 'recruitment.candidates', 'recruitment.budget', 'recruitment.interview', 'recruitment.background', 'recruitment.offer', 'recruitment.risk', 'recruitment.reference', 'recruitment.diversity', 'recruitment.ai', 'recruitment.onboarding',
                    
                    // Onboarding
                    'onboarding.view', 'onboarding.checklist', 'onboarding.policy', 'onboarding.osha', 'onboarding.contract', 'onboarding.template', 'onboarding.probation', 'onboarding.signing',
                    
                    // Attendance & Timesheet
                    'attendance.view', 'attendance.manage', 'attendance.biometric', 'attendance.shift', 'attendance.overtime', 'attendance.absence', 'attendance.leave', 'attendance.alert', 'attendance.productivity', 'attendance.legal',
                    
                    // Payroll Management (limited)
                    'payroll.view', 'payroll.manage', 'payroll.grossnet', 'payroll.paye', 'payroll.nssf', 'payroll.wcf', 'payroll.heslb', 'payroll.deductions', 'payroll.payslip', 'payroll.audit', 'payroll.overtime', 'payroll.holiday', 'payroll.leavepay', 'payroll.final', 'payroll.severance', 'payroll.compliance',
                    
                    // Compensation & Benefits
                    'compensation.view', 'compensation.manage', 'compensation.salary', 'compensation.allowance', 'compensation.benefits', 'compensation.tax', 'compensation.equity', 'compensation.eligibility', 'compensation.medical', 'compensation.pension', 'compensation.loan', 'compensation.incentive',
                    
                    // Performance Management
                    'performance.view', 'performance.manage', 'performance.goals', 'performance.appraisal', 'performance.pip', 'performance.warning', 'performance.kpi', 'performance.okr', 'performance.360', 'performance.improvement', 'performance.discipline', 'performance.promotion', 'performance.talent', 'performance.documentation',
                    
                    // Employee Relations & Discipline (CRITICAL)
                    'discipline.view', 'discipline.manage', 'discipline.incident', 'discipline.investigation', 'discipline.letter', 'discipline.report', 'discipline.misconduct', 'discipline.progressive', 'discipline.evidence', 'discipline.hearing', 'discipline.adjourn', 'discipline.risk', 'discipline.case', 'discipline.timeline', 'discipline.witness', 'discipline.sensitivity', 'discipline.engine', 'discipline.legal', 'discipline.precedent', 'discipline.score', 'discipline.exposure', 'discipline.block',
                    
                    // Compliance & Legal
                    'compliance.view', 'compliance.manage', 'compliance.elra', 'compliance.labour', 'compliance.osha', 'compliance.union', 'compliance.data', 'compliance.database', 'compliance.audit', 'compliance.scan', 'compliance.filing', 'compliance.update', 'compliance.expat', 'compliance.policy', 'compliance.bargaining', 'compliance.dashboard',
                    
                    // Training & Development
                    'training.view', 'training.manage', 'training.needs', 'training.compliance', 'training.records', 'training.skills', 'training.certification', 'training.budget', 'training.history', 'training.compliance', 'training.succession',
                    
                    // Talent Management
                    'talent.view', 'talent.manage', 'talent.succession', 'talent.potential', 'talent.skills',
                    
                    // Workforce Analytics
                    'analytics.view', 'analytics.manage', 'analytics.dashboard', 'analytics.turnover', 'analytics.absenteeism', 'analytics.disciplinary', 'analytics.diversity', 'analytics.payroll', 'analytics.overtime', 'analytics.legal', 'analytics.dispute', 'analytics.risk', 'analytics.satisfaction', 'analytics.predictive',
                    
                    // Case Management
                    'casemanagement.view', 'casemanagement.manage', 'casemanagement.generator', 'casemanagement.chronology', 'casemanagement.evidence', 'casemanagement.witness', 'casemanagement.readiness',
                    
                    // Dashboard & Reports
                    'dashboard.view', 'reports.view', 'reports.create', 'reports.export', 'reports.schedule',
                    
                    // Client Management
                    'clients.view', 'clients.create', 'clients.edit', 'clients.tenant', 'clients.data',
                    
                    // User Management (limited)
                    'users.view', 'users.create', 'users.edit', 'users.roles', 'users.audit',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.permissions',
                ],
            ],
            
            // ROLE 3: HR Officer (Client)
            [
                'name' => 'hr_officer',
                'display_name' => 'HR Officer',
                'description' => 'HR Officer - Client-level HR operations and employee management',
                'permissions' => [
                    // Organization & Workforce
                    'organization.view', 'organization.structure', 'organization.employment',
                    
                    // Employee Master Data
                    'employees.view', 'employees.create', 'employees.edit', 'employees.contract', 'employees.discipline', 'employees.performance', 'employees.training', 'employees.loan', 'employees.nextofkin', 'employees.probation',
                    
                    // Recruitment
                    'recruitment.view', 'recruitment.requisition', 'recruitment.advert', 'recruitment.candidates', 'recruitment.interview', 'recruitment.background', 'recruitment.offer', 'recruitment.reference', 'recruitment.diversity', 'recruitment.onboarding',
                    
                    // Onboarding
                    'onboarding.view', 'onboarding.checklist', 'onboarding.policy', 'onboarding.osha', 'onboarding.contract', 'onboarding.template', 'onboarding.probation', 'onboarding.signing',
                    
                    // Attendance & Timesheet
                    'attendance.view', 'attendance.manage', 'attendance.shift', 'attendance.overtime', 'attendance.absence', 'attendance.leave', 'attendance.alert',
                    
                    // Payroll Management (limited)
                    'payroll.view', 'payroll.manage', 'payroll.deductions', 'payroll.payslip', 'payroll.audit', 'payroll.overtime', 'payroll.holiday', 'payroll.leavepay',
                    
                    // Compensation & Benefits
                    'compensation.view', 'compensation.manage', 'compensation.allowance', 'compensation.benefits', 'compensation.eligibility', 'compensation.medical', 'compensation.pension', 'compensation.loan',
                    
                    // Performance Management
                    'performance.view', 'performance.manage', 'performance.goals', 'performance.appraisal', 'performance.pip', 'performance.warning', 'performance.kpi', 'performance.improvement', 'performance.discipline', 'performance.promotion',
                    
                    // Employee Relations & Discipline (limited)
                    'discipline.view', 'discipline.incident', 'discipline.investigation', 'discipline.letter', 'discipline.report', 'discipline.misconduct', 'discipline.progressive', 'discipline.evidence', 'discipline.hearing', 'discipline.risk', 'discipline.case', 'discipline.timeline',
                    
                    // Compliance & Legal
                    'compliance.view', 'compliance.labour', 'compliance.osha', 'compliance.union', 'compliance.data', 'compliance.scan', 'compliance.filing', 'compliance.expat', 'compliance.policy',
                    
                    // Training & Development
                    'training.view', 'training.manage', 'training.needs', 'training.compliance', 'training.records', 'training.skills', 'training.certification', 'training.history',
                    
                    // Dashboard & Reports
                    'dashboard.view', 'reports.view', 'reports.create', 'reports.export',
                ],
            ],
            
            // ROLE 4: Finance/Payroll Officer
            [
                'name' => 'finance_payroll_officer',
                'display_name' => 'Finance/Payroll Officer',
                'description' => 'Finance/Payroll Officer - Payroll processing and financial reporting',
                'permissions' => [
                    // Employee Master Data (view only)
                    'employees.view', 'employees.edit', 'employees.loan',
                    
                    // Attendance & Timesheet
                    'attendance.view', 'attendance.manage', 'attendance.overtime', 'attendance.leave',
                    
                    // Payroll Management (full)
                    'payroll.view', 'payroll.manage', 'payroll.grossnet', 'payroll.paye', 'payroll.nssf', 'payroll.wcf', 'payroll.heslb', 'payroll.deductions', 'payroll.payslip', 'payroll.audit', 'payroll.overtime', 'payroll.holiday', 'payroll.leavepay', 'payroll.final', 'payroll.severance', 'payroll.termination', 'payroll.compliance',
                    
                    // Compensation & Benefits
                    'compensation.view', 'compensation.manage', 'compensation.salary', 'compensation.allowance', 'compensation.benefits', 'compensation.tax', 'compensation.eligibility', 'compensation.medical', 'compensation.pension', 'compensation.loan', 'compensation.incentive', 'compensation.projection',
                    
                    // Compliance & Legal (financial)
                    'compliance.view', 'compliance.labour', 'compliance.filing',
                    
                    // Dashboard & Reports
                    'dashboard.view', 'reports.view', 'reports.create', 'reports.export', 'reports.schedule',
                ],
            ],
            
            // ROLE 5: Line Manager
            [
                'name' => 'line_manager',
                'display_name' => 'Line Manager',
                'description' => 'Line Manager - Department-level management and team supervision',
                'permissions' => [
                    // Employee Master Data (team only)
                    'employees.view', 'employees.edit',
                    'employees.performance', 'employees.training', 'employees.probation',
                    
                    // Recruitment (team hiring)
                    'recruitment.view', 'recruitment.requisition', 'recruitment.candidates', 'recruitment.interview', 'recruitment.offer', 'recruitment.reference',
                    
                    // Attendance & Timesheet (team)
                    'attendance.view', 'attendance.manage', 'attendance.shift', 'attendance.overtime', 'attendance.absence', 'attendance.leave',
                    
                    // Performance Management (team)
                    'performance.view', 'performance.manage', 'performance.goals', 'performance.appraisal', 'performance.pip', 'performance.warning', 'performance.kpi', 'performance.improvement', 'performance.discipline', 'performance.promotion',
                    
                    // Employee Relations & Discipline (team)
                    'discipline.view', 'discipline.incident', 'discipline.investigation', 'discipline.letter', 'discipline.report', 'discipline.misconduct', 'discipline.progressive', 'discipline.evidence', 'discipline.hearing', 'discipline.risk', 'discipline.case', 'discipline.timeline',
                    
                    // Training & Development (team)
                    'training.view', 'training.manage', 'training.needs', 'training.records', 'training.skills', 'training.certification', 'training.history',
                    
                    // Talent Management (team)
                    'talent.view', 'talent.manage', 'talent.succession', 'talent.potential',
                    
                    // Dashboard & Reports
                    'dashboard.view', 'reports.view', 'reports.create', 'reports.export',
                ],
            ],
            
            // ROLE 6: Employee (Self-service)
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Employee - Self-service access to own data and basic functions',
                'permissions' => [
                    // Employee Self Service
                    'selfservice.view', 'selfservice.profile', 'selfservice.leave', 'selfservice.balance', 'selfservice.training', 'selfservice.payslip', 'selfservice.personal', 'selfservice.contract', 'selfservice.complaint', 'selfservice.performance', 'selfservice.loan', 'selfservice.policy', 'selfservice.tracking',
                    
                    // Basic Views
                    'dashboard.view', 'reports.view',
                ],
            ],
            
            // ROLE 7: External Auditor (Read-only)
            [
                'name' => 'external_auditor',
                'display_name' => 'External Auditor',
                'description' => 'External Auditor - Read-only access for compliance auditing',
                'permissions' => [
                    // Read-only access to all modules
                    'organization.view', 'employees.view', 'recruitment.view', 'onboarding.view', 'attendance.view', 'payroll.view', 'compensation.view', 'performance.view', 'discipline.view', 'compliance.view', 'training.view', 'talent.view', 'analytics.view', 'selfservice.view', 'casemanagement.view', 'dashboard.view', 'reports.view', 'reports.export',
                    
                    // Audit-specific
                    'audit.view', 'audit.trail', 'audit.compliance', 'audit.export',
                    'security.view', 'security.audit', 'security.access',
                    'compliance.view', 'compliance.audit', 'compliance.scan', 'compliance.filing',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'is_active' => true,
                ]
            );

            // Attach permissions
            if (isset($roleData['permissions'])) {
                $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
                $role->permissions()->sync($permissions);
            }
        }
    }
}
