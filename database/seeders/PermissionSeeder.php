<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // MODULE 1: ORGANIZATION & WORKFORCE SETUP
            ['name' => 'organization.view', 'display_name' => 'View Organization', 'group' => 'Organization', 'description' => 'View organization structure and company profile'],
            ['name' => 'organization.setup', 'display_name' => 'Setup Organization', 'group' => 'Organization', 'description' => 'Setup company profile and organizational structure'],
            ['name' => 'organization.sector', 'display_name' => 'Manage Sector Classification', 'group' => 'Organization', 'description' => 'Manage sector risk classification'],
            ['name' => 'organization.structure', 'display_name' => 'Manage Org Structure', 'group' => 'Organization', 'description' => 'Manage organizational structure'],
            ['name' => 'organization.employment', 'display_name' => 'Manage Employment Categories', 'group' => 'Organization', 'description' => 'Manage employment categories per Tanzania law'],
            ['name' => 'organization.union', 'display_name' => 'Manage Union Status', 'group' => 'Organization', 'description' => 'Manage union status information'],
            
            // MODULE 2: EMPLOYEE MASTER DATA
            ['name' => 'employees.view', 'display_name' => 'View Employees', 'group' => 'Employees', 'description' => 'View employee master data'],
            ['name' => 'employees.create', 'display_name' => 'Create Employees', 'group' => 'Employees', 'description' => 'Create new employee records'],
            ['name' => 'employees.edit', 'display_name' => 'Edit Employees', 'group' => 'Employees', 'description' => 'Edit employee information'],
            ['name' => 'employees.delete', 'display_name' => 'Delete Employees', 'group' => 'Employees', 'description' => 'Delete employee records'],
            ['name' => 'employees.contract', 'display_name' => 'Manage Contracts', 'group' => 'Employees', 'description' => 'Manage employee contracts and contract types'],
            ['name' => 'employees.workpermit', 'display_name' => 'Manage Work Permits', 'group' => 'Employees', 'description' => 'Manage non-citizen work permits'],
            ['name' => 'employees.medical', 'display_name' => 'View Medical Records', 'group' => 'Employees', 'description' => 'Access restricted medical records'],
            ['name' => 'employees.discipline', 'display_name' => 'View Disciplinary History', 'group' => 'Employees', 'description' => 'View employee disciplinary history'],
            ['name' => 'employees.performance', 'display_name' => 'View Performance History', 'group' => 'Employees', 'description' => 'View employee performance records'],
            ['name' => 'employees.promotion', 'display_name' => 'Manage Promotions', 'group' => 'Employees', 'description' => 'Manage employee promotions'],
            ['name' => 'employees.training', 'display_name' => 'View Training History', 'group' => 'Employees', 'description' => 'View employee training records'],
            ['name' => 'employees.loan', 'display_name' => 'Manage Loans', 'group' => 'Employees', 'description' => 'Manage employee loan records'],
            ['name' => 'employees.nextofkin', 'display_name' => 'Manage Next of Kin', 'group' => 'Employees', 'description' => 'Manage employee next of kin information'],
            ['name' => 'employees.probation', 'display_name' => 'Manage Probation', 'group' => 'Employees', 'description' => 'Track employee probation periods'],
            ['name' => 'employees.signature', 'display_name' => 'Manage Digital Signatures', 'group' => 'Employees', 'description' => 'Manage employee digital signatures'],
            
            // MODULE 3: TALENT ACQUISITION & RECRUITMENT
            ['name' => 'recruitment.view', 'display_name' => 'View Recruitment', 'group' => 'Recruitment', 'description' => 'View recruitment data'],
            ['name' => 'recruitment.requisition', 'display_name' => 'Manage Job Requisitions', 'group' => 'Recruitment', 'description' => 'Manage job requisition workflow'],
            ['name' => 'recruitment.approval', 'display_name' => 'Approve Vacancies', 'group' => 'Recruitment', 'description' => 'Approve job requisitions and vacancies'],
            ['name' => 'recruitment.advert', 'display_name' => 'Manage Job Adverts', 'group' => 'Recruitment', 'description' => 'Manage job advertisements'],
            ['name' => 'recruitment.candidates', 'display_name' => 'Manage Candidates', 'group' => 'Recruitment', 'description' => 'Manage candidate database'],
            ['name' => 'recruitment.budget', 'display_name' => 'Manage Recruitment Budget', 'group' => 'Recruitment', 'description' => 'Control recruitment budget'],
            ['name' => 'recruitment.interview', 'display_name' => 'Manage Interviews', 'group' => 'Recruitment', 'description' => 'Manage interview scheduling and scoring'],
            ['name' => 'recruitment.background', 'display_name' => 'Manage Background Checks', 'group' => 'Recruitment', 'description' => 'Manage background check process'],
            ['name' => 'recruitment.offer', 'display_name' => 'Generate Offer Letters', 'group' => 'Recruitment', 'description' => 'Generate law-compliant offer letters'],
            ['name' => 'recruitment.risk', 'display_name' => 'Assess Candidate Risk', 'group' => 'Recruitment', 'description' => 'Risk scoring of candidates'],
            ['name' => 'recruitment.reference', 'display_name' => 'Verify References', 'group' => 'Recruitment', 'description' => 'Track reference verification'],
            ['name' => 'recruitment.diversity', 'display_name' => 'Track Diversity', 'group' => 'Recruitment', 'description' => 'Track diversity metrics'],
            ['name' => 'recruitment.ai', 'display_name' => 'Use AI Ranking', 'group' => 'Recruitment', 'description' => 'Use AI-based candidate ranking'],
            ['name' => 'recruitment.onboarding', 'display_name' => 'Manage Digital Onboarding', 'group' => 'Recruitment', 'description' => 'Manage digital onboarding process'],
            
            // MODULE 4: ONBOARDING
            ['name' => 'onboarding.view', 'display_name' => 'View Onboarding', 'group' => 'Onboarding', 'description' => 'View onboarding status'],
            ['name' => 'onboarding.checklist', 'display_name' => 'Manage Induction Checklists', 'group' => 'Onboarding', 'description' => 'Manage induction checklist automation'],
            ['name' => 'onboarding.policy', 'display_name' => 'Track Policy Acknowledgements', 'group' => 'Onboarding', 'description' => 'Track policy acknowledgements'],
            ['name' => 'onboarding.osha', 'display_name' => 'Manage OSHA Induction', 'group' => 'Onboarding', 'description' => 'Manage OSHA induction records'],
            ['name' => 'onboarding.contract', 'display_name' => 'Confirm Contracts', 'group' => 'Onboarding', 'description' => 'Confirm onboarding contracts'],
            ['name' => 'onboarding.template', 'display_name' => 'Manage Contract Templates', 'group' => 'Onboarding', 'description' => 'Manage Tanzania law-compliant templates'],
            ['name' => 'onboarding.probation', 'display_name' => 'Track Probation', 'group' => 'Onboarding', 'description' => 'Automated probation tracking'],
            ['name' => 'onboarding.signing', 'display_name' => 'Manage Digital Signing', 'group' => 'Onboarding', 'description' => 'Manage digital e-signature process'],
            ['name' => 'onboarding.approval', 'display_name' => 'Approve High-Risk Contracts', 'group' => 'Onboarding', 'description' => 'HR Admin approval for high-risk contracts'],
            
            // MODULE 5: ATTENDANCE & TIMESHEET MANAGEMENT
            ['name' => 'attendance.view', 'display_name' => 'View Attendance', 'group' => 'Attendance', 'description' => 'View attendance records'],
            ['name' => 'attendance.manage', 'display_name' => 'Manage Attendance', 'group' => 'Attendance', 'description' => 'Manage attendance data'],
            ['name' => 'attendance.biometric', 'display_name' => 'Manage Biometric Integration', 'group' => 'Attendance', 'description' => 'Manage biometric API integration'],
            ['name' => 'attendance.shift', 'display_name' => 'Manage Shift Scheduling', 'group' => 'Attendance', 'description' => 'Manage shift schedules'],
            ['name' => 'attendance.overtime', 'display_name' => 'Manage Overtime', 'group' => 'Attendance', 'description' => 'Manage overtime tracking and authorization'],
            ['name' => 'attendance.absence', 'display_name' => 'Manage Absences', 'group' => 'Attendance', 'description' => 'Manage absence reasons and tracking'],
            ['name' => 'attendance.leave', 'display_name' => 'Manage Leave Balances', 'group' => 'Attendance', 'description' => 'Manage leave balances and deductions'],
            ['name' => 'attendance.alert', 'display_name' => 'View Attendance Alerts', 'group' => 'Attendance', 'description' => 'View absenteeism and late pattern alerts'],
            ['name' => 'attendance.productivity', 'display_name' => 'View Productivity Scores', 'group' => 'Attendance', 'description' => 'View productivity score index'],
            ['name' => 'attendance.legal', 'display_name' => 'Manage Legal Limits', 'group' => 'Attendance', 'description' => 'Manage working hours and rest day compliance'],
            
            // MODULE 6: PAYROLL MANAGEMENT
            ['name' => 'payroll.view', 'display_name' => 'View Payroll', 'group' => 'Payroll', 'description' => 'View payroll information'],
            ['name' => 'payroll.manage', 'display_name' => 'Manage Payroll', 'group' => 'Payroll', 'description' => 'Manage payroll processing'],
            ['name' => 'payroll.grossnet', 'display_name' => 'Process Gross-to-Net', 'group' => 'Payroll', 'description' => 'Process gross-to-net payroll calculations'],
            ['name' => 'payroll.paye', 'display_name' => 'Manage PAYE', 'group' => 'Payroll', 'description' => 'Manage PAYE calculations and returns'],
            ['name' => 'payroll.nssf', 'display_name' => 'Manage NSSF', 'group' => 'Payroll', 'description' => 'Manage NSSF contributions'],
            ['name' => 'payroll.wcf', 'display_name' => 'Manage WCF', 'group' => 'Payroll', 'description' => 'Manage WCF contributions'],
            ['name' => 'payroll.heslb', 'display_name' => 'Manage HESLB', 'group' => 'Payroll', 'description' => 'Manage HESLB contributions'],
            ['name' => 'payroll.deductions', 'display_name' => 'Manage Deductions', 'group' => 'Payroll', 'description' => 'Manage statutory and other deductions'],
            ['name' => 'payroll.payslip', 'display_name' => 'Generate Payslips', 'group' => 'Payroll', 'description' => 'Generate employee payslips'],
            ['name' => 'payroll.audit', 'display_name' => 'View Payroll Audit', 'group' => 'Payroll', 'description' => 'View payroll audit trail'],
            ['name' => 'payroll.overtime', 'display_name' => 'Calculate Overtime', 'group' => 'Payroll', 'description' => 'Calculate legal overtime formulas'],
            ['name' => 'payroll.holiday', 'display_name' => 'Calculate Holiday Pay', 'group' => 'Payroll', 'description' => 'Calculate public holiday rates'],
            ['name' => 'payroll.leavepay', 'display_name' => 'Calculate Leave Pay', 'group' => 'Payroll', 'description' => 'Calculate leave encashment'],
            ['name' => 'payroll.final', 'display_name' => 'Calculate Final Dues', 'group' => 'Payroll', 'description' => 'Calculate final dues calculator'],
            ['name' => 'payroll.severance', 'display_name' => 'Calculate Severance', 'group' => 'Payroll', 'description' => 'Automated legal severance calculator'],
            ['name' => 'payroll.termination', 'display_name' => 'Simulate Termination', 'group' => 'Payroll', 'description' => 'Termination simulation tool'],
            ['name' => 'payroll.compliance', 'display_name' => 'Ensure Payroll Compliance', 'group' => 'Payroll', 'description' => 'Block payroll if compliance incomplete'],
            
            // MODULE 7: COMPENSATION & BENEFITS
            ['name' => 'compensation.view', 'display_name' => 'View Compensation', 'group' => 'Compensation', 'description' => 'View compensation and benefits'],
            ['name' => 'compensation.manage', 'display_name' => 'Manage Compensation', 'group' => 'Compensation', 'description' => 'Manage compensation structures'],
            ['name' => 'compensation.salary', 'display_name' => 'Manage Salary Structures', 'group' => 'Compensation', 'description' => 'Manage salary bands and structures'],
            ['name' => 'compensation.allowance', 'display_name' => 'Manage Allowances', 'group' => 'Compensation', 'description' => 'Manage allowances and benefits'],
            ['name' => 'compensation.benefits', 'display_name' => 'Manage Benefits', 'group' => 'Compensation', 'description' => 'Manage non-cash benefits'],
            ['name' => 'compensation.tax', 'display_name' => 'Manage Tax Treatment', 'group' => 'Compensation', 'description' => 'Manage Income Tax Act compliance'],
            ['name' => 'compensation.equity', 'display_name' => 'Analyze Internal Equity', 'group' => 'Compensation', 'description' => 'Internal equity analysis'],
            ['name' => 'compensation.eligibility', 'display_name' => 'Manage Benefits Eligibility', 'group' => 'Compensation', 'description' => 'Benefits eligibility engine'],
            ['name' => 'compensation.medical', 'display_name' => 'Manage Medical Insurance', 'group' => 'Compensation', 'description' => 'Manage medical insurance schemes'],
            ['name' => 'compensation.pension', 'display_name' => 'Manage Pension Schemes', 'group' => 'Compensation', 'description' => 'Manage pension scheme tracking'],
            ['name' => 'compensation.loan', 'display_name' => 'Manage Loan Deductions', 'group' => 'Compensation', 'description' => 'Automate loan deductions'],
            ['name' => 'compensation.incentive', 'display_name' => 'Calculate Incentives', 'group' => 'Compensation', 'description' => 'Calculate incentives and bonuses'],
            ['name' => 'compensation.projection', 'display_name' => 'Cost Projection', 'group' => 'Compensation', 'description' => 'Cost projection modeling'],
            
            // MODULE 8: PERFORMANCE MANAGEMENT
            ['name' => 'performance.view', 'display_name' => 'View Performance', 'group' => 'Performance', 'description' => 'View performance management'],
            ['name' => 'performance.manage', 'display_name' => 'Manage Performance', 'group' => 'Performance', 'description' => 'Manage performance processes'],
            ['name' => 'performance.goals', 'display_name' => 'Manage Goals', 'group' => 'Performance', 'description' => 'Manage goal setting and OKRs'],
            ['name' => 'performance.appraisal', 'display_name' => 'Manage Appraisals', 'group' => 'Performance', 'description' => 'Manage performance appraisals'],
            ['name' => 'performance.pip', 'display_name' => 'Manage PIPs', 'group' => 'Performance', 'description' => 'Manage Performance Improvement Plans'],
            ['name' => 'performance.warning', 'display_name' => 'Manage Warnings', 'group' => 'Performance', 'description' => 'Manage performance warnings'],
            ['name' => 'performance.kpi', 'display_name' => 'Manage KPIs', 'group' => 'Performance', 'description' => 'Manage KPI management'],
            ['name' => 'performance.okr', 'display_name' => 'Manage OKRs', 'group' => 'Performance', 'description' => 'Manage OKR integration'],
            ['name' => 'performance.360', 'display_name' => 'Manage 360-Degree Reviews', 'group' => 'Performance', 'description' => 'Manage 360-degree evaluations'],
            ['name' => 'performance.improvement', 'display_name' => 'Track Improvements', 'group' => 'Performance', 'description' => 'Track improvement plan progress'],
            ['name' => 'performance.discipline', 'display_name' => 'Link to Discipline', 'group' => 'Performance', 'description' => 'Link performance to discipline module'],
            ['name' => 'performance.promotion', 'display_name' => 'Assess Promotion Readiness', 'group' => 'Performance', 'description' => 'Promotion readiness scoring'],
            ['name' => 'performance.talent', 'display_name' => 'Manage Talent Pool', 'group' => 'Performance', 'description' => 'Talent pool ranking'],
            ['name' => 'performance.documentation', 'display_name' => 'Ensure Documentation', 'group' => 'Performance', 'description' => 'Prevent arbitrary termination'],
            
            // MODULE 9: EMPLOYEE RELATIONS & DISCIPLINE (CRITICAL)
            ['name' => 'discipline.view', 'display_name' => 'View Discipline', 'group' => 'Discipline', 'description' => 'View discipline and relations'],
            ['name' => 'discipline.manage', 'display_name' => 'Manage Discipline', 'group' => 'Discipline', 'description' => 'Manage discipline processes'],
            ['name' => 'discipline.incident', 'display_name' => 'Manage Incidents', 'group' => 'Discipline', 'description' => 'Manage incident reporting'],
            ['name' => 'discipline.investigation', 'display_name' => 'Manage Investigations', 'group' => 'Discipline', 'description' => 'Manage investigation process'],
            ['name' => 'discipline.letter', 'display_name' => 'Issue Show Cause Letters', 'group' => 'Discipline', 'description' => 'Issue show cause letters'],
            ['name' => 'discipline.report', 'display_name' => 'Manage Investigation Reports', 'group' => 'Discipline', 'description' => 'Manage investigation reports'],
            ['name' => 'discipline.misconduct', 'display_name' => 'Categorize Misconduct', 'group' => 'Discipline', 'description' => 'Misconduct categorization'],
            ['name' => 'discipline.progressive', 'display_name' => 'Manage Progressive Discipline', 'group' => 'Discipline', 'description' => 'Progressive discipline steps'],
            ['name' => 'discipline.evidence', 'display_name' => 'Manage Evidence', 'group' => 'Discipline', 'description' => 'Upload and manage evidence'],
            ['name' => 'discipline.hearing', 'display_name' => 'Manage Hearings', 'group' => 'Discipline', 'description' => 'Schedule and manage hearings'],
            ['name' => 'discipline.adjourn', 'display_name' => 'Adjourn Hearings', 'group' => 'Discipline', 'description' => 'Hearing adjournment management'],
            ['name' => 'discipline.approval', 'display_name' => 'HR Admin Approval', 'group' => 'Discipline', 'description' => 'HR Admin decision approval'],
            ['name' => 'discipline.risk', 'display_name' => 'Risk Scoring', 'group' => 'Discipline', 'description' => 'Risk scoring (LOW/MEDIUM/HIGH)'],
            ['name' => 'discipline.case', 'display_name' => 'Manage Case Files', 'group' => 'Discipline', 'description' => 'Digital case file management'],
            ['name' => 'discipline.timeline', 'display_name' => 'Track Case Timeline', 'group' => 'Discipline', 'description' => 'Case timeline tracking'],
            ['name' => 'discipline.witness', 'display_name' => 'Manage Witness Statements', 'group' => 'Discipline', 'description' => 'Witness statement management'],
            ['name' => 'discipline.sensitivity', 'display_name' => 'Risk Sensitivity Rating', 'group' => 'Discipline', 'description' => 'Risk sensitivity rating (Low/Medium/High/Critical)'],
            ['name' => 'discipline.engine', 'display_name' => 'Use Investigation Engine', 'group' => 'Discipline', 'description' => 'AI-powered investigation questions'],
            ['name' => 'discipline.legal', 'display_name' => 'Legal Risk Analysis', 'group' => 'Discipline', 'description' => 'Legal risk analysis and suggestions'],
            ['name' => 'discipline.precedent', 'display_name' => 'Compare Precedents', 'group' => 'Discipline', 'description' => 'Precedent comparison system'],
            ['name' => 'discipline.score', 'display_name' => 'Decision Risk Score', 'group' => 'Discipline', 'description' => 'Generate decision risk score'],
            ['name' => 'discipline.exposure', 'display_name' => 'Risk Exposure Analysis', 'group' => 'Discipline', 'description' => 'Highlight labour law risk exposure'],
            ['name' => 'discipline.block', 'display_name' => 'Block Illegal Actions', 'group' => 'Discipline', 'description' => 'Block illegal shortcuts'],
            ['name' => 'discipline.suspension', 'display_name' => 'Approve Suspensions', 'group' => 'Discipline', 'description' => 'HR Admin approval for suspensions'],
            ['name' => 'discipline.warning', 'display_name' => 'Approve Warnings', 'group' => 'Discipline', 'description' => 'HR Admin approval for warnings'],
            ['name' => 'discipline.termination', 'display_name' => 'Approve Terminations', 'group' => 'Discipline', 'description' => 'HR Admin approval for terminations'],
            ['name' => 'discipline.outcome', 'display_name' => 'Approve Outcomes', 'group' => 'Discipline', 'description' => 'HR Admin approval for disciplinary outcomes'],
            
            // MODULE 10: COMPLIANCE & LEGAL
            ['name' => 'compliance.view', 'display_name' => 'View Compliance', 'group' => 'Compliance', 'description' => 'View compliance and legal'],
            ['name' => 'compliance.manage', 'display_name' => 'Manage Compliance', 'group' => 'Compliance', 'description' => 'Manage compliance processes'],
            ['name' => 'compliance.elra', 'display_name' => 'Manage ELRA Workflows', 'group' => 'Compliance', 'description' => 'Manage ELRA compliance workflows'],
            ['name' => 'compliance.labour', 'display_name' => 'Labour Act Compliance', 'group' => 'Compliance', 'description' => 'Labour Institutions Act compliance'],
            ['name' => 'compliance.osha', 'display_name' => 'OSHA Compliance', 'group' => 'Compliance', 'description' => 'OSHA compliance records'],
            ['name' => 'compliance.union', 'display_name' => 'Union Engagement', 'group' => 'Compliance', 'description' => 'Union engagement records'],
            ['name' => 'compliance.data', 'display_name' => 'Data Protection', 'group' => 'Compliance', 'description' => 'Data protection compliance'],
            ['name' => 'compliance.database', 'display_name' => 'Labour Law Database', 'group' => 'Compliance', 'description' => 'Tanzania Labour Act database'],
            ['name' => 'compliance.audit', 'display_name' => 'Compliance Audit', 'group' => 'Compliance', 'description' => 'Automated compliance audit checklist'],
            ['name' => 'compliance.scan', 'display_name' => 'Contract Compliance Scan', 'group' => 'Compliance', 'description' => 'Contract compliance scanning'],
            ['name' => 'compliance.filing', 'display_name' => 'Statutory Filing', 'group' => 'Compliance', 'description' => 'Statutory filing reminder system'],
            ['name' => 'compliance.update', 'display_name' => 'Law Update Notification', 'group' => 'Compliance', 'description' => 'Labour law update notifications'],
            ['name' => 'compliance.expat', 'display_name' => 'Expat Compliance', 'group' => 'Compliance', 'description' => 'Expat work permit compliance'],
            ['name' => 'compliance.policy', 'display_name' => 'Policy Compliance', 'group' => 'Compliance', 'description' => 'Policy compliance tracking'],
            ['name' => 'compliance.bargaining', 'display_name' => 'Collective Bargaining', 'group' => 'Compliance', 'description' => 'Collective bargaining tracking'],
            ['name' => 'compliance.dashboard', 'display_name' => 'Compliance Dashboard', 'group' => 'Compliance', 'description' => 'Real-time risk exposure dashboard'],
            
            // MODULE 11: TRAINING & DEVELOPMENT
            ['name' => 'training.view', 'display_name' => 'View Training', 'group' => 'Training', 'description' => 'View training and development'],
            ['name' => 'training.manage', 'display_name' => 'Manage Training', 'group' => 'Training', 'description' => 'Manage training programs'],
            ['name' => 'training.needs', 'display_name' => 'Training Needs Analysis', 'group' => 'Training', 'description' => 'Training needs analysis'],
            ['name' => 'training.compliance', 'display_name' => 'Compliance Training', 'group' => 'Training', 'description' => 'Mandatory compliance training'],
            ['name' => 'training.records', 'display_name' => 'Training Records', 'group' => 'Training', 'description' => 'Legal defense training records'],
            ['name' => 'training.skills', 'display_name' => 'Skills Gap Mapping', 'group' => 'Training', 'description' => 'Skills gap analysis'],
            ['name' => 'training.certification', 'display_name' => 'Certification Tracking', 'group' => 'Training', 'description' => 'Certification tracking'],
            ['name' => 'training.budget', 'display_name' => 'Training Budget', 'group' => 'Training', 'description' => 'Budget allocation'],
            ['name' => 'training.history', 'display_name' => 'Learning History', 'group' => 'Training', 'description' => 'Learning history tracking'],
            ['name' => 'training.compliance', 'display_name' => 'Compliance Tracker', 'group' => 'Training', 'description' => 'Compliance training tracker'],
            ['name' => 'training.succession', 'display_name' => 'Succession Planning', 'group' => 'Training', 'description' => 'Succession planning matrix'],
            
            // MODULE 12: TALENT MANAGEMENT
            ['name' => 'talent.view', 'display_name' => 'View Talent', 'group' => 'Talent', 'description' => 'View talent management'],
            ['name' => 'talent.manage', 'display_name' => 'Manage Talent', 'group' => 'Talent', 'description' => 'Manage talent processes'],
            ['name' => 'talent.succession', 'display_name' => 'Succession Planning', 'group' => 'Talent', 'description' => 'Manage succession planning'],
            ['name' => 'talent.potential', 'display_name' => 'High-Potential ID', 'group' => 'Talent', 'description' => 'Identify high-potential employees'],
            ['name' => 'talent.skills', 'display_name' => 'Skills Inventory', 'group' => 'Talent', 'description' => 'Manage skills inventory'],
            
            // MODULE 13: WORKFORCE ANALYTICS
            ['name' => 'analytics.view', 'display_name' => 'View Analytics', 'group' => 'Analytics', 'description' => 'View workforce analytics'],
            ['name' => 'analytics.manage', 'display_name' => 'Manage Analytics', 'group' => 'Analytics', 'description' => 'Manage analytics configuration'],
            ['name' => 'analytics.dashboard', 'display_name' => 'Executive Dashboard', 'group' => 'Analytics', 'description' => 'View executive dashboard'],
            ['name' => 'analytics.turnover', 'display_name' => 'Turnover Analysis', 'group' => 'Analytics', 'description' => 'Turnover rate and analysis'],
            ['name' => 'analytics.absenteeism', 'display_name' => 'Absenteeism Trends', 'group' => 'Analytics', 'description' => 'Absenteeism trend analysis'],
            ['name' => 'analytics.disciplinary', 'display_name' => 'Disciplinary Trends', 'group' => 'Analytics', 'description' => 'Disciplinary trend analysis'],
            ['name' => 'analytics.diversity', 'display_name' => 'Gender Diversity', 'group' => 'Analytics', 'description' => 'Gender diversity index'],
            ['name' => 'analytics.payroll', 'display_name' => 'Payroll Cost Ratio', 'group' => 'Analytics', 'description' => 'Payroll cost ratio analysis'],
            ['name' => 'analytics.overtime', 'display_name' => 'Overtime Risk', 'group' => 'Analytics', 'description' => 'Overtime risk exposure'],
            ['name' => 'analytics.legal', 'display_name' => 'Legal Case Exposure', 'group' => 'Analytics', 'description' => 'Legal case exposure score'],
            ['name' => 'analytics.dispute', 'display_name' => 'Dispute Probability', 'group' => 'Analytics', 'description' => 'Dispute probability index'],
            ['name' => 'analytics.risk', 'display_name' => 'Legal Risk Index', 'group' => 'Analytics', 'description' => 'Legal risk index'],
            ['name' => 'analytics.satisfaction', 'display_name' => 'Employee Satisfaction', 'group' => 'Analytics', 'description' => 'Employee satisfaction pulse'],
            ['name' => 'analytics.predictive', 'display_name' => 'Predictive Analytics', 'group' => 'Analytics', 'description' => 'Predictive analytics for disputes'],
            
            // MODULE 14: EMPLOYEE SELF-SERVICE
            ['name' => 'selfservice.view', 'display_name' => 'View Self Service', 'group' => 'Self Service', 'description' => 'View self service portal'],
            ['name' => 'selfservice.profile', 'display_name' => 'Edit Profile', 'group' => 'Self Service', 'description' => 'Edit own profile'],
            ['name' => 'selfservice.leave', 'display_name' => 'Manage Leave', 'group' => 'Self Service', 'description' => 'Apply and manage leave requests'],
            ['name' => 'selfservice.balance', 'display_name' => 'View Leave Balance', 'group' => 'Self Service', 'description' => 'View leave balances'],
            ['name' => 'selfservice.training', 'display_name' => 'View Training Records', 'group' => 'Self Service', 'description' => 'View training records'],
            ['name' => 'selfservice.payslip', 'display_name' => 'Download Payslips', 'group' => 'Self Service', 'description' => 'Download payslips'],
            ['name' => 'selfservice.personal', 'display_name' => 'Update Personal Info', 'group' => 'Self Service', 'description' => 'Update personal information'],
            ['name' => 'selfservice.contract', 'display_name' => 'View Contracts', 'group' => 'Self Service', 'description' => 'View employment contracts'],
            ['name' => 'selfservice.complaint', 'display_name' => 'File Complaints', 'group' => 'Self Service', 'description' => 'File complaints and grievances'],
            ['name' => 'selfservice.performance', 'display_name' => 'View Performance', 'group' => 'Self Service', 'description' => 'View performance reviews'],
            ['name' => 'selfservice.loan', 'display_name' => 'Track Loans', 'group' => 'Self Service', 'description' => 'Track loan balances'],
            ['name' => 'selfservice.policy', 'display_name' => 'Access Policies', 'group' => 'Self Service', 'description' => 'Access policy documents'],
            ['name' => 'selfservice.tracking', 'display_name' => 'Legal Tracking', 'group' => 'Self Service', 'description' => 'All submissions legally tracked'],
            
            // MODULE 15: CASE MANAGEMENT (CMA / COURT READY)
            ['name' => 'casemanagement.view', 'display_name' => 'View Case Management', 'group' => 'Case Management', 'description' => 'View case management'],
            ['name' => 'casemanagement.manage', 'display_name' => 'Manage Cases', 'group' => 'Case Management', 'description' => 'Manage legal cases'],
            ['name' => 'casemanagement.generator', 'display_name' => 'Generate Case Files', 'group' => 'Case Management', 'description' => 'Case file generator'],
            ['name' => 'casemanagement.chronology', 'display_name' => 'Build Chronology', 'group' => 'Case Management', 'description' => 'Chronology builder'],
            ['name' => 'casemanagement.evidence', 'display_name' => 'Manage Evidence Bundles', 'group' => 'Case Management', 'description' => 'Evidence bundle management'],
            ['name' => 'casemanagement.witness', 'display_name' => 'Manage Witness Lists', 'group' => 'Case Management', 'description' => 'Witness list management'],
            ['name' => 'casemanagement.readiness', 'display_name' => 'Case Readiness Score', 'group' => 'Case Management', 'description' => 'Case readiness scoring'],
            
            // MODULE 16: SECURITY & DATA PROTECTION
            ['name' => 'security.view', 'display_name' => 'View Security', 'group' => 'Security', 'description' => 'View security settings'],
            ['name' => 'security.manage', 'display_name' => 'Manage Security', 'group' => 'Security', 'description' => 'Manage security configuration'],
            ['name' => 'security.encryption', 'display_name' => 'Manage Encryption', 'group' => 'Security', 'description' => 'End-to-end encryption management'],
            ['name' => 'security.audit', 'display_name' => 'View Audit Logs', 'group' => 'Security', 'description' => 'View audit logs'],
            ['name' => 'security.access', 'display_name' => 'View Access Logs', 'group' => 'Security', 'description' => 'View data access logs'],
            ['name' => 'security.consent', 'display_name' => 'Manage Consent', 'group' => 'Security', 'description' => 'Consent records management'],
            ['name' => 'security.backup', 'display_name' => 'Manage Backups', 'group' => 'Security', 'description' => 'Backup and disaster recovery'],
            ['name' => 'security.roles', 'display_name' => 'Manage Role Access', 'group' => 'Security', 'description' => 'Role-based access control'],
            ['name' => 'security.masking', 'display_name' => 'Data Masking', 'group' => 'Security', 'description' => 'Data masking configuration'],
            ['name' => 'security.activity', 'display_name' => 'View Activity Logs', 'group' => 'Security', 'description' => 'Activity logs monitoring'],
            ['name' => 'security.mfa', 'display_name' => 'Manage MFA', 'group' => 'Security', 'description' => 'Multi-factor authentication'],
            ['name' => 'security.incident', 'display_name' => 'Manage Incidents', 'group' => 'Security', 'description' => 'Incident response log'],
            
            // SYSTEM ADMINISTRATION
            ['name' => 'system.view', 'display_name' => 'View System Admin', 'group' => 'System', 'description' => 'View system administration'],
            ['name' => 'system.manage', 'display_name' => 'System Administration', 'group' => 'System', 'description' => 'Full system administration'],
            ['name' => 'system.backup', 'display_name' => 'System Backups', 'group' => 'System', 'description' => 'System backup management'],
            ['name' => 'system.maintenance', 'display_name' => 'System Maintenance', 'group' => 'System', 'description' => 'System maintenance mode'],
            ['name' => 'system.monitoring', 'display_name' => 'System Monitoring', 'group' => 'System', 'description' => 'System performance monitoring'],
            ['name' => 'system.integration', 'display_name' => 'Manage Integrations', 'group' => 'System', 'description' => 'API and system integrations'],
            
            // DASHBOARD AND REPORTS
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'group' => 'Dashboard', 'description' => 'View main dashboard'],
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'group' => 'Reports', 'description' => 'View reports and analytics'],
            ['name' => 'reports.create', 'display_name' => 'Create Reports', 'group' => 'Reports', 'description' => 'Create custom reports'],
            ['name' => 'reports.export', 'display_name' => 'Export Reports', 'group' => 'Reports', 'description' => 'Export reports in various formats'],
            ['name' => 'reports.schedule', 'display_name' => 'Schedule Reports', 'group' => 'Reports', 'description' => 'Schedule automated reports'],
            
            // CLIENT MANAGEMENT (MULTI-TENANT)
            ['name' => 'clients.view', 'display_name' => 'View Clients', 'group' => 'Clients', 'description' => 'View client list'],
            ['name' => 'clients.create', 'display_name' => 'Create Clients', 'group' => 'Clients', 'description' => 'Create new clients'],
            ['name' => 'clients.edit', 'display_name' => 'Edit Clients', 'group' => 'Clients', 'description' => 'Edit existing clients'],
            ['name' => 'clients.delete', 'display_name' => 'Delete Clients', 'group' => 'Clients', 'description' => 'Delete clients'],
            ['name' => 'clients.tenant', 'display_name' => 'Manage Tenancy', 'group' => 'Clients', 'description' => 'Manage multi-tenant separation'],
            ['name' => 'clients.data', 'display_name' => 'Data Separation', 'group' => 'Clients', 'description' => 'Ensure strict data separation'],
            
            // USER MANAGEMENT
            ['name' => 'users.view', 'display_name' => 'View Users', 'group' => 'Users', 'description' => 'View user list'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'group' => 'Users', 'description' => 'Create new users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'group' => 'Users', 'description' => 'Edit existing users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'group' => 'Users', 'description' => 'Delete users'],
            ['name' => 'users.roles', 'display_name' => 'Manage User Roles', 'group' => 'Users', 'description' => 'Assign roles to users'],
            ['name' => 'users.permissions', 'display_name' => 'Manage User Permissions', 'group' => 'Users', 'description' => 'Assign specific permissions to users'],
            ['name' => 'users.audit', 'display_name' => 'View User Audit', 'group' => 'Users', 'description' => 'View user activity audit'],
            
            // ROLE MANAGEMENT
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'group' => 'Roles', 'description' => 'View role list'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'group' => 'Roles', 'description' => 'Create new roles'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'group' => 'Roles', 'description' => 'Edit existing roles'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'group' => 'Roles', 'description' => 'Delete roles'],
            ['name' => 'roles.permissions', 'display_name' => 'Manage Role Permissions', 'group' => 'Roles', 'description' => 'Assign permissions to roles'],
            ['name' => 'roles.audit', 'display_name' => 'View Role Audit', 'group' => 'Roles', 'description' => 'View role assignment audit'],
            
            // PERMISSION MANAGEMENT
            ['name' => 'permissions.view', 'display_name' => 'View Permissions', 'group' => 'Permissions', 'description' => 'View permission list'],
            ['name' => 'permissions.create', 'display_name' => 'Create Permissions', 'group' => 'Permissions', 'description' => 'Create new permissions'],
            ['name' => 'permissions.edit', 'display_name' => 'Edit Permissions', 'group' => 'Permissions', 'description' => 'Edit existing permissions'],
            ['name' => 'permissions.delete', 'display_name' => 'Delete Permissions', 'group' => 'Permissions', 'description' => 'Delete permissions'],
            ['name' => 'permissions.audit', 'display_name' => 'View Permission Audit', 'group' => 'Permissions', 'description' => 'View permission usage audit'],
            
            // NOTIFICATIONS AND ALERTS
            ['name' => 'notifications.view', 'display_name' => 'View Notifications', 'group' => 'Notifications', 'description' => 'View system notifications'],
            ['name' => 'notifications.manage', 'display_name' => 'Manage Notifications', 'group' => 'Notifications', 'description' => 'Manage notification settings'],
            ['name' => 'notifications.alerts', 'display_name' => 'Manage Alerts', 'group' => 'Notifications', 'description' => 'Manage system alerts'],
            ['name' => 'notifications.automated', 'display_name' => 'Automated Alerts', 'group' => 'Notifications', 'description' => 'Configure automated alerts'],
            
            // AUDIT AND COMPLIANCE
            ['name' => 'audit.view', 'display_name' => 'View Audit Logs', 'group' => 'Audit', 'description' => 'View comprehensive audit logs'],
            ['name' => 'audit.trail', 'display_name' => 'Audit Trail', 'group' => 'Audit', 'description' => 'Complete audit trail'],
            ['name' => 'audit.compliance', 'display_name' => 'Compliance Audit', 'group' => 'Audit', 'description' => 'Compliance audit reports'],
            ['name' => 'audit.export', 'display_name' => 'Export Audit', 'group' => 'Audit', 'description' => 'Export audit data'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
