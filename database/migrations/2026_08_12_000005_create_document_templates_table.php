<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('document_templates')) {
            Schema::create('document_templates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('template_name');
                $table->string('template_type'); // appointment, warning, show_cause, outcome, appeal, payslip, etc.
                $table->text('html_content');
                $table->text('css_content')->nullable();
                $table->json('variables')->nullable(); // Available variables for the template
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                
                $table->index(['client_id', 'template_type']);
                $table->index(['client_id', 'is_active']);
            });
        }

        // Insert default document templates for Tanzania HR
        DB::table('document_templates')->insert([
            [
                'client_id' => null,
                'template_name' => 'Appointment Letter',
                'template_type' => 'appointment',
                'html_content' => $this->getAppointmentLetterTemplate(),
                'css_content' => $this->getDefaultCss(),
                'variables' => json_encode(['employee_name', 'position', 'department', 'start_date', 'basic_salary', 'reporting_to', 'company_name']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'template_name' => 'First Warning Letter',
                'template_type' => 'warning_first',
                'html_content' => $this->getFirstWarningTemplate(),
                'css_content' => $this->getDefaultCss(),
                'variables' => json_encode(['employee_name', 'case_number', 'incident_date', 'incident_description', 'warning_date', 'expiry_date', 'company_name']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'template_name' => 'Show Cause Notice',
                'template_type' => 'show_cause',
                'html_content' => $this->getShowCauseTemplate(),
                'css_content' => $this->getDefaultCss(),
                'variables' => json_encode(['employee_name', 'case_number', 'incident_date', 'incident_description', 'response_deadline', 'company_name']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'template_name' => 'Disciplinary Outcome Letter',
                'template_type' => 'outcome',
                'html_content' => $this->getOutcomeTemplate(),
                'css_content' => $this->getDefaultCss(),
                'variables' => json_encode(['employee_name', 'case_number', 'outcome_type', 'outcome_date', 'rationale', 'appeal_deadline', 'company_name']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => null,
                'template_name' => 'Payslip',
                'template_type' => 'payslip',
                'html_content' => $this->getPayslipTemplate(),
                'css_content' => $this->getDefaultCss(),
                'variables' => json_encode(['employee_name', 'employee_id', 'payroll_period', 'basic_salary', 'overtime_pay', 'allowances', 'gross_pay', 'paye', 'nssf', 'net_pay', 'company_name']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }

    private function getAppointmentLetterTemplate()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Letter</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1>{{company_name}}</h1>
            <p>Appointment Letter</p>
        </div>
        
        <p>Date: {{current_date}}</p>
        <p>To: {{employee_name}}</p>
        
        <p>Dear {{employee_name}},</p>
        
        <p>We are pleased to offer you the position of <strong>{{position}}</strong> in the <strong>{{department}}</strong> department, effective from <strong>{{start_date}}</strong>.</p>
        
        <p>Your terms of employment are as follows:</p>
        <ul>
            <li>Position: {{position}}</li>
            <li>Department: {{department}}</li>
            <li>Basic Salary: TZS {{basic_salary}}</li>
            <li>Reporting To: {{reporting_to}}</li>
        </ul>
        
        <p>Please sign below to accept this offer.</p>
        
        <br><br>
        <p>__________________________</p>
        <p>Employee Signature</p>
        
        <br><br>
        <p>__________________________</p>
        <p>Company Representative</p>
    </div>
</body>
</html>';
    }

    private function getFirstWarningTemplate()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>First Warning Letter</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1>{{company_name}}</h1>
            <p>First Written Warning</p>
        </div>
        
        <p>Date: {{warning_date}}</p>
        <p>Case Number: {{case_number}}</p>
        <p>To: {{employee_name}}</p>
        
        <p>Dear {{employee_name}},</p>
        
        <p>This letter serves as a first written warning regarding the following incident that occurred on {{incident_date}}:</p>
        
        <p><em>{{incident_description}}</em></p>
        
        <p>This warning will remain active for 6 months from the date of this letter. Any further violations during this period may result in escalated disciplinary action.</p>
        
        <p>Warning Expiry Date: {{expiry_date}}</p>
        
        <br><br>
        <p>__________________________</p>
        <p>HR Manager</p>
        
        <br><br>
        <p>__________________________</p>
        <p>Employee Acknowledgement</p>
    </div>
</body>
</html>';
    }

    private function getShowCauseTemplate()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Show Cause Notice</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1>{{company_name}}</h1>
            <p>Show Cause Notice</p>
        </div>
        
        <p>Date: {{current_date}}</p>
        <p>Case Number: {{case_number}}</p>
        <p>To: {{employee_name}}</p>
        
        <p>Dear {{employee_name}},</p>
        
        <p>This notice is issued in relation to the following incident that occurred on {{incident_date}}:</p>
        
        <p><em>{{incident_description}}</em></p>
        
        <p>You are hereby required to show cause within 48 hours (by {{response_deadline}}) why disciplinary action should not be taken against you.</p>
        
        <p>Please submit your written response to the HR department by the stated deadline.</p>
        
        <br><br>
        <p>__________________________</p>
        <p>HR Manager</p>
    </div>
</body>
</html>';
    }

    private function getOutcomeTemplate()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Disciplinary Outcome Letter</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1>{{company_name}}</h1>
            <p>Disciplinary Outcome</p>
        </div>
        
        <p>Date: {{outcome_date}}</p>
        <p>Case Number: {{case_number}}</p>
        <p>To: {{employee_name}}</p>
        
        <p>Dear {{employee_name}},</p>
        
        <p>Following the disciplinary hearing regarding the incident on {{incident_date}}, the following outcome has been determined:</p>
        
        <p><strong>Outcome: {{outcome_type}}</strong></p>
        
        <p><strong>Rationale:</strong></p>
        <p>{{rationale}}</p>
        
        <p>You have the right to appeal this decision within 5 working days (by {{appeal_deadline}}). Any appeal should be submitted in writing to the HR department.</p>
        
        <br><br>
        <p>__________________________</p>
        <p>Disciplinary Authority</p>
    </div>
</body>
</html>';
    }

    private function getPayslipTemplate()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payslip</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1>{{company_name}}</h1>
            <p>Payslip for {{payroll_period}}</p>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Employee Name:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{employee_name}}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Employee ID:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{employee_id}}</td>
            </tr>
        </table>
        
        <h3>Earnings</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Description</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Amount (TZS)</th>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Basic Salary</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{basic_salary}}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Overtime Pay</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{overtime_pay}}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Allowances</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{allowances}}</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Gross Pay</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;"><strong>{{gross_pay}}</strong></td>
            </tr>
        </table>
        
        <h3>Deductions</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Description</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Amount (TZS)</th>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">PAYE</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{paye}}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">NSSF</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{nssf}}</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Net Pay</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;"><strong>{{net_pay}}</strong></td>
            </tr>
        </table>
        
        <p style="text-align: center; margin-top: 30px;">This is a computer-generated payslip. No signature required.</p>
    </div>
</body>
</html>';
    }

    private function getDefaultCss()
    {
        return 'body { font-family: Arial, sans-serif; line-height: 1.6; } table { width: 100%; border-collapse: collapse; } th, td { padding: 8px; border: 1px solid #ddd; } th { background-color: #f2f2f2; }';
    }
};
