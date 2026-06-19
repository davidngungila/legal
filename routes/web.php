<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientSwitchController;
use App\Http\Controllers\TestLoginController;
use App\Http\Controllers\SelfServiceController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\CompensationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserRegistrationController;
use App\Http\Controllers\ClientRegistrationController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\HrCompetencyInterviewController;
use App\Http\Controllers\TechnicalInterviewController;
use App\Http\Controllers\EmployeeRegistrationController;
use App\Http\Controllers\EmployeeDocumentController;
use App\Http\Controllers\SocialRecordsController;
use App\Http\Controllers\InductionTrainingController;
use App\Http\Controllers\PersonnelIdController;
use App\Http\Controllers\ContractManagementController;
use App\Http\Controllers\EmploymentContractsController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\DisciplinaryController;
use App\Http\Controllers\ExitController;
use App\Http\Controllers\PerformanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to "web" middleware group. Make something great!
|
*/

// Root route - redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware(['guest', 'web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/splash', function () {
        return view('auth.splash');
    })->name('splash');
    Route::get('/sample-users', function () {
        return view('auth.sample-users');
    })->name('sample-users');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.post');
});
Route::middleware(['web'])->post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware(['web', 'auth', \App\Http\Middleware\ShareCurrentUser::class, \App\Http\Middleware\SetCurrentClient::class, \App\Http\Middleware\FilterByCurrentClient::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    // Case Management Routes
    Route::prefix('casemanagement')->group(function () {
        Route::get('/', [CaseController::class, 'index'])->name('casemanagement.index')->middleware('permission:casemanagement.view');
        Route::post('/', [CaseController::class, 'store'])->name('casemanagement.store')->middleware('permission:casemanagement.manage');
        Route::put('/{case}', [CaseController::class, 'update'])->name('casemanagement.update')->middleware('permission:casemanagement.manage');
        Route::get('/export', [CaseController::class, 'export'])->name('casemanagement.export')->middleware('permission:casemanagement.view');
        Route::post('/templates', [CaseController::class, 'storeTemplate'])->name('casemanagement.templates.store')->middleware('permission:casemanagement.manage');
        Route::put('/templates/{index}', [CaseController::class, 'updateTemplate'])->name('casemanagement.templates.update')->middleware('permission:casemanagement.manage');
    });


    // Organization Routes
    Route::prefix('organization')->group(function () {
        Route::get('/setup', function () {
            return view('organization.index');
        })->name('organization.setup');
    });

    // User Management Routes
    Route::prefix('users')->group(function () {
        Route::get('/', function () {
            $clientId = session('current_client_id');
            $currentClient = $clientId ? \App\Models\Client::find($clientId) : null;
            return view('users.index', compact('currentClient'));
        })->name('users.index');
        
        Route::get('/create', function () {
            return view('users.create');
        })->name('users.create');

        Route::prefix('data')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.data.index');
            Route::post('/', [UserController::class, 'store'])->name('users.data.store');
            Route::get('/roles-permissions', [UserController::class, 'getRolesAndPermissions'])->name('users.data.roles-permissions');
            Route::post('/bulk', [UserController::class, 'bulkOperations'])->name('users.data.bulk');
            Route::get('/{id}', [UserController::class, 'show'])->name('users.data.show');
            Route::put('/{id}', [UserController::class, 'update'])->name('users.data.update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.data.destroy');
        });

        Route::get('/export', [UserController::class, 'export'])->name('users.export');
    });

    // Role Management Routes
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::get('/permissions/list', [RoleController::class, 'getPermissions'])->name('permissions');
    });

    // Permission Management Routes
    Route::prefix('permissions')->group(function () {
        Route::get('/', function () {
            return view('permissions.index');
        })->name('permissions.index');
        
        Route::get('/create', function () {
            return view('permissions.create');
        })->name('permissions.create');
    });

    Route::prefix('recruitment')->group(function () {
        Route::get('/', function () {
            return view('recruitment.index');
        })->name('recruitment.index');
    });

    // Time & Attendance Routes
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/upsert', [AttendanceController::class, 'upsert'])->name('attendance.upsert');
        Route::post('/import', [AttendanceController::class, 'importTimesheet'])->name('attendance.import');
        Route::get('/calendar', [AttendanceController::class, 'calendar'])->name('attendance.calendar');
        Route::get('/timesheets', [AttendanceController::class, 'timesheets'])->name('attendance.timesheets');
        Route::get('/shifts', [AttendanceController::class, 'shifts'])->name('attendance.shifts');
        Route::get('/violations', [AttendanceController::class, 'violations'])->name('attendance.violations');
    });

    // Payroll Routes
    Route::prefix('payroll')->group(function () {
                Route::get('/', [PayrollController::class, 'index'])->name('payroll.index');
                Route::get('/data', [PayrollController::class, 'data'])->name('payroll.data');
                Route::post('/generate-from-attendance', [PayrollController::class, 'generateFromAttendance'])->name('payroll.generate.from.attendance');
                Route::get('/upload', [PayrollController::class, 'showUploadForm'])->name('payroll.upload');
                Route::post('/upload', [PayrollController::class, 'uploadCsv'])->name('payroll.upload.csv');
                Route::get('/template', [PayrollController::class, 'downloadTemplate'])->name('payroll.template');
                Route::get('/payslip', function () {
                    return view('payroll.payslip');
                })->name('payroll.payslip');
                Route::get('/reports', [PayrollController::class, 'reports'])->name('payroll.reports');
                Route::get('/{id}', [PayrollController::class, 'show'])->name('payroll.show');
                Route::put('/{payroll}', [PayrollController::class, 'update'])->name('payroll.update');
                Route::put('/{id}/status', [PayrollController::class, 'updateStatus'])->name('payroll.update.status');
                Route::delete('/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
            });

            Route::prefix('leave')->name('leave.')->group(function () {
                Route::get('/', [App\Http\Controllers\LeaveController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\LeaveController::class, 'store'])->name('store');
                Route::put('/{leaveRequest}', [App\Http\Controllers\LeaveController::class, 'updateStatus'])->name('updateStatus');
                Route::get('/balances', [App\Http\Controllers\LeaveController::class, 'balances'])->name('balances');
                Route::get('/calendar', [App\Http\Controllers\LeaveController::class, 'calendar'])->name('calendar');
                Route::get('/reports', [App\Http\Controllers\LeaveController::class, 'reports'])->name('reports');
            });

            Route::prefix('compensation')->group(function () {
                Route::get('/', [CompensationController::class, 'index'])->name('compensation.index');
                Route::get('/export', [CompensationController::class, 'export'])->name('compensation.export');
                Route::get('/employees', [CompensationController::class, 'employees'])->name('compensation.employees');
                Route::put('/employees/{employee}', [CompensationController::class, 'updateEmployee'])->name('compensation.employees.update');
                Route::get('/salary-structures', [CompensationController::class, 'salaryStructures'])->name('compensation.salary-structures');
                Route::get('/merit-review', [CompensationController::class, 'meritReview'])->name('compensation.merit-review');
                Route::get('/allowances', [CompensationController::class, 'allowances'])->name('compensation.allowances');
                Route::get('/loans', [CompensationController::class, 'loans'])->name('compensation.loans');
            });

    // Performance Routes
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [PerformanceController::class, 'index'])->name('index');
        Route::post('/', [PerformanceController::class, 'store'])->name('store');
        Route::put('/{review}', [PerformanceController::class, 'updateStatus'])->name('update');
        Route::get('/goals', [PerformanceController::class, 'goals'])->name('goals');
        Route::get('/pip', [PerformanceController::class, 'pip'])->name('pip');
        Route::get('/analytics', [PerformanceController::class, 'analytics'])->name('analytics');
    });

    // Employee Relations & Discipline Routes
    Route::prefix('discipline')->name('discipline.')->group(function () {
        Route::get('/', [App\Http\Controllers\DisciplinaryController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\DisciplinaryController::class, 'store'])->name('store');
        Route::get('/investigations', [App\Http\Controllers\DisciplinaryController::class, 'investigations'])->name('investigations');
        Route::get('/hearings', [App\Http\Controllers\DisciplinaryController::class, 'hearings'])->name('hearings');
        Route::get('/documents', [App\Http\Controllers\DisciplinaryController::class, 'documents'])->name('documents');
    });

    // Exit Management Routes
    Route::prefix('exit')->name('exit.')->group(function () {
        Route::get('/', [App\Http\Controllers\ExitController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\ExitController::class, 'store'])->name('store');
    });

    // Compliance Routes
    Route::prefix('compliance')->group(function () {
        Route::get('/', [ComplianceController::class, 'index'])->name('compliance.index');
        Route::post('/audit', [ComplianceController::class, 'runAudit'])->name('compliance.audit');
        Route::get('/reports', [ComplianceController::class, 'getReports'])->name('compliance.reports');
        Route::get('/download', [ComplianceController::class, 'downloadReport'])->name('compliance.download');
        Route::get('/statutory-filings', [ComplianceController::class, 'statutoryFilings'])->name('compliance.statutory-filings');
        Route::get('/deadlines', [ComplianceController::class, 'deadlines'])->name('compliance.deadlines');
    });

    // HRIS - User Registration Routes
    Route::prefix('user-registration')->name('user-registration.')->group(function () {
        Route::get('/', [UserRegistrationController::class, 'index'])->name('index');
        Route::get('/create', [UserRegistrationController::class, 'create'])->name('create');
        Route::post('/', [UserRegistrationController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserRegistrationController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserRegistrationController::class, 'update'])->name('update');
        Route::post('/{user}/deactivate', [UserRegistrationController::class, 'deactivate'])->name('deactivate');
        Route::post('/{user}/activate', [UserRegistrationController::class, 'activate'])->name('activate');
    });

    // HRIS - Client Registration Routes
    Route::prefix('client-registration')->name('client-registration.')->group(function () {
        Route::get('/', [ClientRegistrationController::class, 'index'])->name('index');
        Route::get('/create', [ClientRegistrationController::class, 'create'])->name('create');
        Route::post('/', [ClientRegistrationController::class, 'store'])->name('store');
        Route::get('/{client}/edit', [ClientRegistrationController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientRegistrationController::class, 'update'])->name('update');
        Route::post('/{client}/deactivate', [ClientRegistrationController::class, 'deactivate'])->name('deactivate');
        Route::post('/{client}/activate', [ClientRegistrationController::class, 'activate'])->name('activate');
    });

    // HRIS - Job Vacancy Routes
    Route::prefix('job-vacancy')->name('job-vacancy.')->group(function () {
        Route::get('/', [JobVacancyController::class, 'index'])->name('index');
        Route::get('/create', [JobVacancyController::class, 'create'])->name('create');
        Route::post('/', [JobVacancyController::class, 'store'])->name('store');
        Route::get('/{jobVacancy}', [JobVacancyController::class, 'show'])->name('show');
        Route::get('/{jobVacancy}/edit', [JobVacancyController::class, 'edit'])->name('edit');
        Route::put('/{jobVacancy}', [JobVacancyController::class, 'update'])->name('update');
        Route::delete('/{jobVacancy}', [JobVacancyController::class, 'destroy'])->name('destroy');
        Route::post('/{jobVacancy}/submit', [JobVacancyController::class, 'submit'])->name('submit');
        Route::post('/{jobVacancy}/approve', [JobVacancyController::class, 'approve'])->name('approve');
        Route::post('/{jobVacancy}/reject', [JobVacancyController::class, 'reject'])->name('reject');
        Route::post('/{jobVacancy}/upload-shortlisted', [JobVacancyController::class, 'uploadShortlistedFile'])->name('upload-shortlisted');
        Route::post('/{jobVacancy}/upload-signed', [JobVacancyController::class, 'uploadSignedFile'])->name('upload-signed');
        Route::post('/{jobVacancy}/close', [JobVacancyController::class, 'close'])->name('close');
    });

    // HRIS - HR Competency Interview Routes
    Route::prefix('hr-interview')->name('hr-interview.')->group(function () {
        Route::get('/', [HrCompetencyInterviewController::class, 'index'])->name('index');
        Route::get('/create', [HrCompetencyInterviewController::class, 'create'])->name('create');
        Route::post('/', [HrCompetencyInterviewController::class, 'store'])->name('store');
        Route::get('/{hrCompetencyInterview}', [HrCompetencyInterviewController::class, 'show'])->name('show');
        Route::get('/{hrCompetencyInterview}/edit', [HrCompetencyInterviewController::class, 'edit'])->name('edit');
        Route::put('/{hrCompetencyInterview}', [HrCompetencyInterviewController::class, 'update'])->name('update');
        Route::post('/{hrCompetencyInterview}/submit', [HrCompetencyInterviewController::class, 'submit'])->name('submit');
        Route::post('/{hrCompetencyInterview}/approve', [HrCompetencyInterviewController::class, 'approve'])->name('approve');
        Route::post('/{hrCompetencyInterview}/reject', [HrCompetencyInterviewController::class, 'reject'])->name('reject');
        Route::post('/{hrCompetencyInterview}/upload-signed', [HrCompetencyInterviewController::class, 'uploadSignedFile'])->name('upload-signed');
        Route::post('/{hrCompetencyInterview}/generate-pdf', [HrCompetencyInterviewController::class, 'generatePdf'])->name('generate-pdf');
    });

    // HRIS - Technical Interview Routes
    Route::prefix('technical-interview')->name('technical-interview.')->group(function () {
        Route::get('/', [TechnicalInterviewController::class, 'index'])->name('index');
        Route::get('/create', [TechnicalInterviewController::class, 'create'])->name('create');
        Route::post('/', [TechnicalInterviewController::class, 'store'])->name('store');
        Route::get('/{technicalInterview}', [TechnicalInterviewController::class, 'show'])->name('show');
        Route::get('/{technicalInterview}/edit', [TechnicalInterviewController::class, 'edit'])->name('edit');
        Route::put('/{technicalInterview}', [TechnicalInterviewController::class, 'update'])->name('update');
        Route::post('/{technicalInterview}/submit', [TechnicalInterviewController::class, 'submit'])->name('submit');
        Route::post('/{technicalInterview}/approve', [TechnicalInterviewController::class, 'approve'])->name('approve');
        Route::post('/{technicalInterview}/reject', [TechnicalInterviewController::class, 'reject'])->name('reject');
        Route::post('/{technicalInterview}/upload-assessment', [TechnicalInterviewController::class, 'uploadAssessmentReport'])->name('upload-assessment');
        Route::post('/{technicalInterview}/upload-signed', [TechnicalInterviewController::class, 'uploadSignedFile'])->name('upload-signed');
        Route::post('/{technicalInterview}/generate-pdf', [TechnicalInterviewController::class, 'generatePdf'])->name('generate-pdf');
    });

    // HRIS - Employee Registration Routes
    Route::prefix('employee-registration')->name('employee-registration.')->group(function () {
        Route::get('/', [EmployeeRegistrationController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeRegistrationController::class, 'create'])->name('create');
        Route::post('/', [EmployeeRegistrationController::class, 'store'])->name('store');
        Route::get('/{employeeRegistration}', [EmployeeRegistrationController::class, 'show'])->name('show');
        Route::get('/{employeeRegistration}/edit', [EmployeeRegistrationController::class, 'edit'])->name('edit');
        Route::put('/{employeeRegistration}', [EmployeeRegistrationController::class, 'update'])->name('update');
        Route::post('/{employeeRegistration}/submit', [EmployeeRegistrationController::class, 'submit'])->name('submit');
        Route::post('/{employeeRegistration}/approve', [EmployeeRegistrationController::class, 'approve'])->name('approve');
        Route::post('/{employeeRegistration}/reject', [EmployeeRegistrationController::class, 'reject'])->name('reject');
        Route::post('/{employeeRegistration}/upload-signed', [EmployeeRegistrationController::class, 'uploadSignedDocument'])->name('upload-signed');
        Route::post('/{employeeRegistration}/generate-pdf', [EmployeeRegistrationController::class, 'generatePdf'])->name('generate-pdf');
    });

    // HRIS - Employee Document Management Routes
    Route::prefix('employee-document')->name('employee-document.')->group(function () {
        Route::get('/', [EmployeeDocumentController::class, 'index'])->name('index');
        Route::get('/employee/{employee}', [EmployeeDocumentController::class, 'employeeDocuments'])->name('employee-documents');
        Route::post('/', [EmployeeDocumentController::class, 'store'])->name('store');
        Route::get('/{employeeDocument}', [EmployeeDocumentController::class, 'show'])->name('show');
        Route::put('/{employeeDocument}', [EmployeeDocumentController::class, 'update'])->name('update');
        Route::post('/{employeeDocument}/verify', [EmployeeDocumentController::class, 'verify'])->name('verify');
        Route::post('/{employeeDocument}/reject', [EmployeeDocumentController::class, 'reject'])->name('reject');
        Route::delete('/{employeeDocument}', [EmployeeDocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{employeeDocument}/download', [EmployeeDocumentController::class, 'download'])->name('download');
        Route::get('/statistics', [EmployeeDocumentController::class, 'statistics'])->name('statistics');
        Route::get('/requiring-attention', [EmployeeDocumentController::class, 'requiringAttention'])->name('requiring-attention');
    });

    // HRIS - Social Records Registration Routes
    Route::prefix('social-records')->name('social-records.')->group(function () {
        Route::get('/', [SocialRecordsController::class, 'index'])->name('index');
        Route::get('/employee/{employee}', [SocialRecordsController::class, 'employeeRecords'])->name('employee-records');
        Route::post('/', [SocialRecordsController::class, 'store'])->name('store');
        Route::put('/{employee}', [SocialRecordsController::class, 'update'])->name('update');
        Route::post('/{employee}/generate-report', [SocialRecordsController::class, 'generateReport'])->name('generate-report');
        Route::get('/statistics', [SocialRecordsController::class, 'statistics'])->name('statistics');
        Route::get('/missing-records', [SocialRecordsController::class, 'missingRecords'])->name('missing-records');
        Route::post('/{employee}/upload-document', [SocialRecordsController::class, 'uploadDocument'])->name('upload-document');
        Route::get('/{employee}/download/{documentType}', [SocialRecordsController::class, 'downloadDocument'])->name('download-document');
    });

    // HRIS - Induction Training Routes
    Route::prefix('induction-training')->name('induction-training.')->group(function () {
        Route::get('/', [InductionTrainingController::class, 'index'])->name('index');
        Route::get('/employee/{employee}', [InductionTrainingController::class, 'employeeTraining'])->name('employee-training');
        Route::post('/', [InductionTrainingController::class, 'store'])->name('store');
        Route::put('/{employee}', [InductionTrainingController::class, 'update'])->name('update');
        Route::post('/{employee}/generate-certificate', [InductionTrainingController::class, 'generateCertificate'])->name('generate-certificate');
        Route::get('/statistics', [InductionTrainingController::class, 'statistics'])->name('statistics');
        Route::get('/requiring-training', [InductionTrainingController::class, 'requiringTraining'])->name('requiring-training');
        Route::post('/schedule-training', [InductionTrainingController::class, 'scheduleTraining'])->name('schedule-training');
        Route::post('/{employee}/upload-materials', [InductionTrainingController::class, 'uploadMaterials'])->name('upload-materials');
        Route::get('/{employee}/download-materials/{materialId}', [InductionTrainingController::class, 'downloadMaterials'])->name('download-materials');
        Route::get('/calendar', [InductionTrainingController::class, 'calendar'])->name('calendar');
    });

    // HRIS - Personnel ID Application Routes
    Route::prefix('personnel-id')->name('personnel-id.')->group(function () {
        Route::get('/', [PersonnelIdController::class, 'index'])->name('index');
        Route::get('/employee/{employee}', [PersonnelIdController::class, 'employeeId'])->name('employee-id');
        Route::post('/', [PersonnelIdController::class, 'store'])->name('store');
        Route::put('/{employee}', [PersonnelIdController::class, 'update'])->name('update');
        Route::post('/{employee}/approve', [PersonnelIdController::class, 'approve'])->name('approve');
        Route::post('/{employee}/reject', [PersonnelIdController::class, 'reject'])->name('reject');
        Route::post('/{employee}/issue', [PersonnelIdController::class, 'issue'])->name('issue');
        Route::post('/{employee}/report-lost', [PersonnelIdController::class, 'reportLost'])->name('report-lost');
        Route::post('/{employee}/generate-card', [PersonnelIdController::class, 'generateCard'])->name('generate-card');
        Route::get('/statistics', [PersonnelIdController::class, 'statistics'])->name('statistics');
        Route::get('/requiring-attention', [PersonnelIdController::class, 'requiringAttention'])->name('requiring-attention');
        Route::post('/{employee}/upload-photo', [PersonnelIdController::class, 'uploadPhoto'])->name('upload-photo');
    });

    // HRIS - Contract Management Routes
    Route::prefix('contract-management')->name('contract-management.')->group(function () {
        Route::get('/', [ContractManagementController::class, 'index'])->name('index');
        Route::get('/employee/{employee}', [ContractManagementController::class, 'employeeContracts'])->name('employee-contracts');
        Route::post('/', [ContractManagementController::class, 'store'])->name('store');
        Route::put('/{employee}', [ContractManagementController::class, 'update'])->name('update');
        Route::post('/{employee}/activate', [ContractManagementController::class, 'activate'])->name('activate');
        Route::post('/{employee}/terminate', [ContractManagementController::class, 'terminate'])->name('terminate');
        Route::post('/{employee}/renew', [ContractManagementController::class, 'renew'])->name('renew');
        Route::post('/{employee}/generate-report', [ContractManagementController::class, 'generateReport'])->name('generate-report');
        Route::get('/statistics', [ContractManagementController::class, 'statistics'])->name('statistics');
        Route::get('/requiring-attention', [ContractManagementController::class, 'requiringAttention'])->name('requiring-attention');
        Route::post('/{employee}/upload-document', [ContractManagementController::class, 'uploadDocument'])->name('upload-document');
        Route::get('/{employee}/download-document/{documentType}', [ContractManagementController::class, 'downloadDocument'])->name('download-document');
        Route::get('/calendar', [ContractManagementController::class, 'calendar'])->name('calendar');
    });

    // HRIS - Employment Contracts Routes
    Route::prefix('employment-contracts')->name('employment-contracts.')->group(function () {
        Route::get('/', [EmploymentContractsController::class, 'index'])->name('index');
        Route::get('/employee/{employee}', [EmploymentContractsController::class, 'employeeContracts'])->name('employee-contracts');
        Route::post('/', [EmploymentContractsController::class, 'store'])->name('store');
        Route::put('/{employee}', [EmploymentContractsController::class, 'update'])->name('update');
        Route::post('/{employee}/activate', [EmploymentContractsController::class, 'activate'])->name('activate');
        Route::post('/{employee}/terminate', [EmploymentContractsController::class, 'terminate'])->name('terminate');
        Route::post('/{employee}/renew', [EmploymentContractsController::class, 'renew'])->name('renew');
        Route::post('/{employee}/generate-pdf', [EmploymentContractsController::class, 'generatePdf'])->name('generate-pdf');
        Route::get('/statistics', [EmploymentContractsController::class, 'statistics'])->name('statistics');
        Route::get('/requiring-attention', [EmploymentContractsController::class, 'requiringAttention'])->name('requiring-attention');
        Route::post('/{employee}/upload-document', [EmploymentContractsController::class, 'uploadDocument'])->name('upload-document');
        Route::get('/{employee}/download-document/{documentType}', [EmploymentContractsController::class, 'downloadDocument'])->name('download-document');
        Route::get('/calendar', [EmploymentContractsController::class, 'calendar'])->name('calendar');
    });

    // HRIS - Workflow System Routes
    Route::prefix('workflow')->name('workflow.')->group(function () {
        Route::get('/', [WorkflowController::class, 'index'])->name('index');
        Route::get('/statistics', [WorkflowController::class, 'statistics'])->name('statistics');
        Route::get('/pending-approvals', [WorkflowController::class, 'pendingApprovals'])->name('pending-approvals');
        Route::get('/history', [WorkflowController::class, 'history'])->name('history');
        Route::post('/approve', [WorkflowController::class, 'approve'])->name('approve');
        Route::post('/reject', [WorkflowController::class, 'reject'])->name('reject');
        Route::post('/forward', [WorkflowController::class, 'forward'])->name('forward');
        Route::get('/details/{workflowId}', [WorkflowController::class, 'details'])->name('details');
        Route::post('/add-comment', [WorkflowController::class, 'addComment'])->name('add-comment');
        Route::get('/calendar', [WorkflowController::class, 'calendar'])->name('calendar');
        Route::get('/analytics', [WorkflowController::class, 'analytics'])->name('analytics');
    });

    // HRIS Dashboard Route
    Route::get('/hris', [DashboardController::class, 'hrisDashboard'])->name('hris.dashboard');

    // Training Routes
    Route::prefix('training')->group(function () {
        Route::get('/', function () {
            return view('training.index');
        })->name('training.index');
        Route::get('/plans', function () {
            return view('training.plans');
        })->name('training.plans');
        Route::get('/completions', function () {
            return view('training.completions');
        })->name('training.completions');
    });

    // Benefits Routes
    Route::prefix('benefits')->name('benefits.')->group(function () {
        Route::get('/enrollment', function () {
            return view('benefits.enrollment');
        })->name('enrollment');
        Route::get('/life-events', function () {
            return view('benefits.life-events');
        })->name('life-events');
        Route::get('/plans', function () {
            return view('benefits.plans');
        })->name('plans');
    });

    // Succession Routes
    Route::prefix('succession')->name('succession.')->group(function () {
        Route::get('/talent-pools', function () {
            return view('succession.talent-pools');
        })->name('talent-pools');
        Route::get('/readiness', function () {
            return view('succession.readiness');
        })->name('readiness');
        Route::get('/career-paths', function () {
            return view('succession.career-paths');
        })->name('career-paths');
    });

    // Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', function () {
            return view('analytics.index');
        })->name('index');
        Route::get('/hr-intelligence', function () {
            return view('analytics.hr-intelligence');
        })->name('hr-intelligence');
        Route::get('/predictive', function () {
            return view('analytics.predictive');
        })->name('predictive');
    });

    // Departments Routes
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DepartmentsController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\DepartmentsController::class, 'store'])->name('store');
        Route::put('/{department}', [\App\Http\Controllers\DepartmentsController::class, 'update'])->name('update');
        Route::delete('/{department}', [\App\Http\Controllers\DepartmentsController::class, 'destroy'])->name('destroy');
    });

    // Positions Routes
    Route::prefix('positions')->name('positions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PositionsController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\PositionsController::class, 'store'])->name('store');
        Route::put('/{position}', [\App\Http\Controllers\PositionsController::class, 'update'])->name('update');
        Route::delete('/{position}', [\App\Http\Controllers\PositionsController::class, 'destroy'])->name('destroy');
    });

    // Audit Trail Routes
    Route::prefix('audit-trail')->name('audit-trail.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AuditController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\AuditController::class, 'export'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\AuditController::class, 'show'])->name('show');
    });

    // Analytics Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/', function () {
            return view('analytics.index');
        })->name('analytics.index');
    });

    // Onboarding Routes
    Route::prefix('onboarding')->group(function () {
        Route::get('/', [OnboardingController::class, 'index'])->name('onboarding.index');
        Route::post('/start', [OnboardingController::class, 'startOnboarding'])->name('onboarding.start');
        Route::post('/complete/{employeeId}', [OnboardingController::class, 'completeOnboarding'])->name('onboarding.complete');
        Route::get('/progress/{employeeId}', [OnboardingController::class, 'getProgress'])->name('onboarding.progress');
    });

    // Employee Self Service Routes
    Route::prefix('selfservice')->group(function () {
        Route::get('/', [SelfServiceController::class, 'index'])->name('selfservice.index');
        Route::get('/leave', [SelfServiceController::class, 'leave'])->name('selfservice.leave');
        Route::post('/leave', [SelfServiceController::class, 'storeLeave'])->name('selfservice.leave.store');
        Route::get('/payslip', [SelfServiceController::class, 'payslip'])->name('selfservice.payslip');
        Route::post('/payslip', [SelfServiceController::class, 'requestPayslip'])->name('selfservice.payslip.request');
        Route::get('/contract', [SelfServiceController::class, 'contract'])->name('selfservice.contract');
        Route::post('/contract', [SelfServiceController::class, 'requestContract'])->name('selfservice.contract.request');
        Route::get('/complaint', [SelfServiceController::class, 'complaint'])->name('selfservice.complaint');
        Route::post('/complaint', [SelfServiceController::class, 'storeComplaint'])->name('selfservice.complaint.store');
        Route::get('/profile', [SelfServiceController::class, 'profile'])->name('selfservice.profile');
        Route::post('/profile', [SelfServiceController::class, 'updateProfile'])->name('selfservice.profile.update');
        Route::get('/expense', [SelfServiceController::class, 'expense'])->name('selfservice.expense');
        Route::post('/expense', [SelfServiceController::class, 'storeExpense'])->name('selfservice.expense.store');
    });

    // Case Management Routes
    Route::prefix('casemanagement')->group(function () {
        Route::get('/', [CaseController::class, 'index'])->name('casemanagement.index');
        Route::post('/', [CaseController::class, 'store'])->name('casemanagement.store');
        Route::put('/{case}', [CaseController::class, 'update'])->name('casemanagement.update');
        Route::get('/export', [CaseController::class, 'export'])->name('casemanagement.export');
        Route::post('/templates', [CaseController::class, 'storeTemplate'])->name('casemanagement.templates.store');
        Route::put('/templates/{index}', [CaseController::class, 'updateTemplate'])->name('casemanagement.templates.update');
    });

    // Documents & Policies Routes
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentsController::class, 'index'])->name('documents.index');
        Route::get('/view/{id}', [DocumentsController::class, 'view'])->name('documents.view');
        Route::get('/download/{id}', [DocumentsController::class, 'download'])->name('documents.download');
        Route::get('/category/{category}', [DocumentsController::class, 'byCategory'])->name('documents.category');
        Route::get('/type/{type}', [DocumentsController::class, 'byType'])->name('documents.type');
        Route::post('/search', [DocumentsController::class, 'search'])->name('documents.search');
    });

    // Client Management Routes
    Route::prefix('clients')->group(function () {
        Route::get('/', function () {
            return view('clients.index');
        })->name('clients.index');
        
        Route::get('/create', function () {
            return view('clients.create');
        })->name('clients.create');
        
        Route::get('/edit', function () {
            return view('clients.edit');
        })->name('clients.edit');
    });

    // API Routes without middleware for testing
    Route::prefix('api')->group(function () {
        // Permissions API
        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index']);
            Route::post('/', [PermissionController::class, 'store']);
            Route::get('/{id}', [PermissionController::class, 'show']);
            Route::put('/{id}', [PermissionController::class, 'update']);
            Route::delete('/{id}', [PermissionController::class, 'destroy']);
        });
        
        // Clients API
        Route::prefix('clients')->group(function () {
            Route::get('/', [ClientController::class, 'index']);
            Route::post('/', [ClientController::class, 'store']);
            Route::get('/{id}', [ClientController::class, 'show']);
            Route::put('/{id}', [ClientController::class, 'update']);
            Route::delete('/{id}', [ClientController::class, 'destroy']);
            Route::post('/bulk', [ClientController::class, 'bulkOperations']);
            Route::get('/export', [ClientController::class, 'export']);
            Route::get('/statistics', [ClientController::class, 'statistics']);
        });

        // Client Switching API
        Route::prefix('client-switch')->group(function () {
            Route::post('/switch', [ClientSwitchController::class, 'switch']);
            Route::get('/current', [ClientSwitchController::class, 'current']);
            Route::get('/available', [ClientSwitchController::class, 'available']);
        });

        // HRIS Stats API
        Route::get('/hris/stats', [DashboardController::class, 'getHrisStats']);
    });

    // Test route for authentication
Route::get('/test-login', [TestLoginController::class, 'testLogin']);

// Test route
    Route::get('/test', function () {
        return view('test');
    });

    // Profile and Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
    Route::post('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings');
    Route::get('/profile/activity', [ProfileController::class, 'activityLog'])->name('profile.activity');
    Route::get('/profile/export', [ProfileController::class, 'export'])->name('profile.export');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/general', [SettingsController::class, 'updateGeneral'])->name('general');
        Route::get('/notifications', function () {
            $clientId = session('current_client_id');
            $currentClient = $clientId ? \App\Models\Client::find($clientId) : null;
            return view('settings.index', compact('currentClient'));
        })->name('notifications');
        Route::post('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.post');
        Route::post('/privacy', [SettingsController::class, 'updatePrivacy'])->name('privacy');
        Route::post('/appearance', [SettingsController::class, 'updateAppearance'])->name('appearance');
        Route::post('/security', [SettingsController::class, 'updateSecurity'])->name('security');
        Route::post('/data', [SettingsController::class, 'updateDataStorage'])->name('data');
        Route::post('/integrations', [SettingsController::class, 'updateIntegrations'])->name('integrations');
        Route::post('/reset', [SettingsController::class, 'resetToDefault'])->name('reset');
        Route::get('/export', [SettingsController::class, 'export'])->name('export');
        Route::get('/data', [SettingsController::class, 'getSettings'])->name('data');
    });

    Route::get('/help', [HelpController::class, 'index'])->name('help');
    Route::post('/help/search', [HelpController::class, 'search'])->name('help.search');
    Route::post('/help/ticket', [HelpController::class, 'createTicket'])->name('help.ticket.create');
    Route::get('/help/tickets', [HelpController::class, 'getTickets'])->name('help.tickets');
    Route::get('/help/ticket/{ticketNumber}', [HelpController::class, 'getTicket'])->name('help.ticket');
    Route::post('/help/ticket/{ticketNumber}/response', [HelpController::class, 'addResponse'])->name('help.ticket.response');
    Route::post('/help/ticket/{ticketNumber}/close', [HelpController::class, 'closeTicket'])->name('help.ticket.close');
    Route::get('/help/article/{id}', [HelpController::class, 'getArticle'])->name('help.article');
    Route::get('/help/stats', [HelpController::class, 'getStats'])->name('help.stats');
    Route::get('/help/contact', [HelpController::class, 'getContactInfo'])->name('help.contact');

    // Employee Management Routes
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/employees/{employee}/generate-contract', [EmployeeController::class, 'generateContract'])->name('employees.generate.contract');
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');
    Route::get('/employees/statistics', [EmployeeController::class, 'statistics'])->name('employees.statistics');

    // Contract Management Routes
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
    Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
    Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
    Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
    Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
    Route::post('/contracts/{contract}/sign', [ContractController::class, 'sign'])->name('contracts.sign');
    Route::post('/contracts/{contract}/terminate', [ContractController::class, 'terminate'])->name('contracts.terminate');
    Route::post('/contracts/{contract}/renew', [ContractController::class, 'renew'])->name('contracts.renew');
    Route::get('/contracts/{contract}/download', [ContractController::class, 'download'])->name('contracts.download');
    Route::get('/contracts/expiring', [ContractController::class, 'expiringSoon'])->name('contracts.expiring');
    Route::get('/contracts/statistics', [ContractController::class, 'statistics'])->name('contracts.statistics');
});
