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

// Test route to verify authentication
Route::get('/test-auth', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? auth()->user()->email : 'none',
        'session_client_id' => session('current_client_id'),
    ]);
})->middleware('web');

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
            Route::get('/next-employee-id', [UserController::class, 'getNextEmployeeId'])->name('users.data.next-employee-id');
            Route::get('/departments/{clientId}', [UserController::class, 'getDepartmentsByClient'])->name('users.data.departments-by-client');
            Route::get('/positions/{departmentId}', [UserController::class, 'getPositionsByDepartment'])->name('users.data.positions-by-department');
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
        Route::post('/shifts', [AttendanceController::class, 'storeShift'])->name('attendance.shifts.store');
        Route::put('/shifts/{shift}', [AttendanceController::class, 'updateShift'])->name('attendance.shifts.update');
        Route::delete('/shifts/{shift}', [AttendanceController::class, 'destroyShift'])->name('attendance.shifts.destroy');
        Route::get('/violations', [AttendanceController::class, 'violations'])->name('attendance.violations');
        Route::put('/violations/{violation}', [AttendanceController::class, 'updateViolation'])->name('attendance.violations.update');
        Route::put('/violations/{violation}/close', [AttendanceController::class, 'closeViolation'])->name('attendance.violations.close');
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
                Route::get('/balances', [App\Http\Controllers\LeaveController::class, 'balances'])->name('balances');
                Route::get('/calendar', [App\Http\Controllers\LeaveController::class, 'calendar'])->name('calendar');
                Route::get('/reports', [App\Http\Controllers\LeaveController::class, 'reports'])->name('reports');
                Route::get('/{id}', [App\Http\Controllers\LeaveController::class, 'show'])->name('show');
                Route::put('/{id}', [App\Http\Controllers\LeaveController::class, 'updateStatus'])->name('updateStatus');
                Route::post('/{id}/approve', [App\Http\Controllers\LeaveController::class, 'approve'])->name('approve');
                Route::post('/{id}/reject', [App\Http\Controllers\LeaveController::class, 'reject'])->name('reject');
                Route::delete('/{id}', [App\Http\Controllers\LeaveController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('compensation')->group(function () {
                Route::get('/', [CompensationController::class, 'index'])->name('compensation.index');
                Route::get('/export', [CompensationController::class, 'export'])->name('compensation.export');
                Route::get('/employees', [CompensationController::class, 'employees'])->name('compensation.employees');
                Route::put('/employees/{employee}', [CompensationController::class, 'updateEmployee'])->name('compensation.employees.update');
                Route::get('/salary-structures', [CompensationController::class, 'salaryStructures'])->name('compensation.salary-structures');
                Route::post('/salary-structures', [CompensationController::class, 'storeSalaryStructure'])->name('compensation.salary-structures.store');
                Route::put('/salary-structures/{id}', [CompensationController::class, 'updateSalaryStructure'])->name('compensation.salary-structures.update');
                Route::delete('/salary-structures/{id}', [CompensationController::class, 'destroySalaryStructure'])->name('compensation.salary-structures.destroy');
                Route::get('/merit-review', [CompensationController::class, 'meritReview'])->name('compensation.merit-review');
                Route::post('/merit-review', [CompensationController::class, 'storeMeritReview'])->name('compensation.merit-review.store');
                Route::put('/merit-review/{id}', [CompensationController::class, 'updateMeritReview'])->name('compensation.merit-review.update');
                Route::delete('/merit-review/{id}', [CompensationController::class, 'destroyMeritReview'])->name('compensation.merit-review.destroy');
                Route::get('/allowances', [CompensationController::class, 'allowances'])->name('compensation.allowances');
                Route::post('/allowances', [CompensationController::class, 'storeAllowance'])->name('compensation.allowances.store');
                Route::put('/allowances/{id}', [CompensationController::class, 'updateAllowance'])->name('compensation.allowances.update');
                Route::delete('/allowances/{id}', [CompensationController::class, 'destroyAllowance'])->name('compensation.allowances.destroy');
                Route::get('/loans', [CompensationController::class, 'loans'])->name('compensation.loans');
                Route::post('/loans', [CompensationController::class, 'storeLoan'])->name('compensation.loans.store');
                Route::put('/loans/{id}', [CompensationController::class, 'updateLoan'])->name('compensation.loans.update');
                Route::delete('/loans/{id}', [CompensationController::class, 'destroyLoan'])->name('compensation.loans.destroy');
            });

    // Performance Routes
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [PerformanceController::class, 'index'])->name('index');
        Route::post('/', [PerformanceController::class, 'store'])->name('store');
        Route::put('/{review}', [PerformanceController::class, 'updateStatus'])->name('update');
        Route::get('/{review}/show', [PerformanceController::class, 'show'])->name('show');
        Route::post('/{review}/ratings', [PerformanceController::class, 'storeRatings'])->name('ratings.store');
        Route::get('/analytics', [PerformanceController::class, 'analytics'])->name('analytics');

        // Performance Cycles
        Route::get('/cycles', [App\Http\Controllers\PerformanceCycleController::class, 'index'])->name('cycles.index');
        Route::post('/cycles', [App\Http\Controllers\PerformanceCycleController::class, 'store'])->name('cycles.store');
        Route::put('/cycles/{cycle}', [App\Http\Controllers\PerformanceCycleController::class, 'update'])->name('cycles.update');
        Route::delete('/cycles/{cycle}', [App\Http\Controllers\PerformanceCycleController::class, 'destroy'])->name('cycles.destroy');

        // Goals & KPIs
        Route::get('/goals', [App\Http\Controllers\EmployeeGoalController::class, 'index'])->name('goals.index');
        Route::post('/goals', [App\Http\Controllers\EmployeeGoalController::class, 'store'])->name('goals.store');
        Route::put('/goals/{goal}', [App\Http\Controllers\EmployeeGoalController::class, 'update'])->name('goals.update');
        Route::delete('/goals/{goal}', [App\Http\Controllers\EmployeeGoalController::class, 'destroy'])->name('goals.destroy');
        Route::post('/goals/{goal}/kpis', [App\Http\Controllers\EmployeeGoalController::class, 'storeKpi'])->name('goals.kpis.store');
        Route::put('/goals/{goal}/kpis/{kpi}', [App\Http\Controllers\EmployeeGoalController::class, 'updateKpi'])->name('goals.kpis.update');
        Route::delete('/goals/{goal}/kpis/{kpi}', [App\Http\Controllers\EmployeeGoalController::class, 'destroyKpi'])->name('goals.kpis.destroy');

        // PIP
        Route::get('/pip', [App\Http\Controllers\PerformancePipController::class, 'index'])->name('pip.index');
        Route::post('/pip', [App\Http\Controllers\PerformancePipController::class, 'store'])->name('pip.store');
        Route::put('/pip/{pip}', [App\Http\Controllers\PerformancePipController::class, 'update'])->name('pip.update');
        Route::delete('/pip/{pip}', [App\Http\Controllers\PerformancePipController::class, 'destroy'])->name('pip.destroy');
        Route::post('/pip/{pip}/reviews', [App\Http\Controllers\PerformancePipController::class, 'storeReview'])->name('pip.reviews.store');
        Route::delete('/pip/{pip}/reviews/{review}', [App\Http\Controllers\PerformancePipController::class, 'destroyReview'])->name('pip.reviews.destroy');

        // Calibration
        Route::get('/calibration', [App\Http\Controllers\CalibrationSessionController::class, 'index'])->name('calibration.index');
        Route::post('/calibration', [App\Http\Controllers\CalibrationSessionController::class, 'store'])->name('calibration.store');
        Route::put('/calibration/{session}', [App\Http\Controllers\CalibrationSessionController::class, 'update'])->name('calibration.update');
        Route::delete('/calibration/{session}', [App\Http\Controllers\CalibrationSessionController::class, 'destroy'])->name('calibration.destroy');
    });

    // Employee Relations & Discipline Routes
    Route::prefix('discipline')->name('discipline.')->group(function () {
        Route::get('/', [App\Http\Controllers\DisciplinaryController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\DisciplinaryController::class, 'store'])->name('store');
        Route::put('/{case}/status', [App\Http\Controllers\DisciplinaryController::class, 'updateStatus'])->name('update-status');
        Route::put('/{case}', [App\Http\Controllers\DisciplinaryController::class, 'update'])->name('update');
        Route::delete('/{case}', [App\Http\Controllers\DisciplinaryController::class, 'destroy'])->name('destroy');
        Route::get('/investigations', [App\Http\Controllers\DisciplinaryController::class, 'investigations'])->name('investigations');
        Route::post('/investigations/{id}/start', [App\Http\Controllers\DisciplinaryController::class, 'startInvestigation'])->name('investigations.start');
        Route::put('/investigations/{id}/update', [App\Http\Controllers\DisciplinaryController::class, 'updateInvestigation'])->name('investigations.update');
        Route::post('/investigations/{id}/hearing', [App\Http\Controllers\DisciplinaryController::class, 'scheduleHearing'])->name('investigations.hearing');
        Route::get('/hearings', [App\Http\Controllers\DisciplinaryController::class, 'hearings'])->name('hearings');
        Route::post('/hearings', [App\Http\Controllers\DisciplinaryController::class, 'storeHearing'])->name('hearings.store');
        Route::get('/documents', [App\Http\Controllers\DisciplinaryController::class, 'documents'])->name('documents');
        Route::post('/documents', [App\Http\Controllers\DisciplinaryController::class, 'storeDocument'])->name('documents.store');
    });

    // Exit Management Routes
    Route::prefix('exit')->name('exit.')->group(function () {
        Route::get('/', [App\Http\Controllers\ExitController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\ExitController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\ExitController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\ExitController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/status', [App\Http\Controllers\ExitController::class, 'updateStatus'])->name('status');
        Route::post('/{id}/checklist', [App\Http\Controllers\ExitController::class, 'storeChecklist'])->name('checklist.store');
        Route::patch('/{id}/checklist/{checklistId}', [App\Http\Controllers\ExitController::class, 'toggleChecklist'])->name('checklist.toggle');
        Route::delete('/{id}/checklist/{checklistId}', [App\Http\Controllers\ExitController::class, 'destroyChecklist'])->name('checklist.destroy');
        Route::post('/{id}/settlement', [App\Http\Controllers\ExitController::class, 'storeSettlement'])->name('settlement.store');
        Route::post('/{id}/settlement/paid', [App\Http\Controllers\ExitController::class, 'markSettlementPaid'])->name('settlement.paid');
    });

    // Compliance Routes
    Route::prefix('compliance')->group(function () {
        Route::get('/', [ComplianceController::class, 'index'])->name('compliance.index');
        Route::post('/audit', [ComplianceController::class, 'runAudit'])->name('compliance.audit');
        Route::get('/reports', [ComplianceController::class, 'getReports'])->name('compliance.reports');
        Route::get('/download', [ComplianceController::class, 'downloadReport'])->name('compliance.download');
        Route::get('/statutory-filings', [ComplianceController::class, 'statutoryFilings'])->name('compliance.statutory-filings');
        Route::get('/deadlines', [ComplianceController::class, 'deadlines'])->name('compliance.deadlines');
        
        // Statutory Filings API
        Route::post('/filings', [ComplianceController::class, 'storeFiling'])->name('compliance.filings.store');
        Route::get('/filings/{id}', [ComplianceController::class, 'getFiling'])->name('compliance.filings.show');
        Route::put('/filings/{id}', [ComplianceController::class, 'updateFiling'])->name('compliance.filings.update');
        Route::delete('/filings/{id}', [ComplianceController::class, 'deleteFiling'])->name('compliance.filings.destroy');
        Route::post('/filings/{id}/submit', [ComplianceController::class, 'markFilingSubmitted'])->name('compliance.filings.submit');
        Route::get('/filings/export', [ComplianceController::class, 'exportFilings'])->name('compliance.filings.export');
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
        Route::get('/{technicalInterview}/generate-pdf', [TechnicalInterviewController::class, 'generatePdf'])->name('generate-pdf');
        Route::get('/{technicalInterview}/download-pdf', [TechnicalInterviewController::class, 'downloadPdf'])->name('download-pdf');
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
        Route::get('/employee-contracts', [ContractManagementController::class, 'employeeContracts'])->name('employee-contracts');
        Route::post('/{contract}/activate', [ContractManagementController::class, 'activate'])->name('activate');
        Route::post('/{contract}/terminate', [ContractManagementController::class, 'terminate'])->name('terminate');
        Route::post('/{contract}/renew', [ContractManagementController::class, 'renew'])->name('renew');
        Route::post('/generate-report', [ContractManagementController::class, 'generateReport'])->name('generate-report');
        Route::get('/statistics', [ContractManagementController::class, 'statistics'])->name('statistics');
        Route::get('/requiring-attention', [ContractManagementController::class, 'requiringAttention'])->name('requiring-attention');
        Route::get('/calendar', [ContractManagementController::class, 'calendar'])->name('calendar');
    });

    // HRIS - Employment Contracts Routes
    Route::prefix('employment-contracts')->name('employment-contracts.')->group(function () {
        Route::get('/', [EmploymentContractsController::class, 'index'])->name('index');
        Route::get('/employee/{employee}', [EmploymentContractsController::class, 'employeeContracts'])->name('employee-contracts');
        Route::get('/{contract}/edit', [EmploymentContractsController::class, 'edit'])->name('edit');
        Route::post('/', [EmploymentContractsController::class, 'store'])->name('store');
        Route::put('/{contract}', [EmploymentContractsController::class, 'update'])->name('update');
        Route::post('/{contract}/activate', [EmploymentContractsController::class, 'activate'])->name('activate');
        Route::post('/{contract}/terminate', [EmploymentContractsController::class, 'terminate'])->name('terminate');
        Route::post('/{contract}/renew', [EmploymentContractsController::class, 'renew'])->name('renew');
        Route::post('/{contract}/generate-pdf', [EmploymentContractsController::class, 'generatePdf'])->name('generate-pdf');
        Route::get('/statistics', [EmploymentContractsController::class, 'statistics'])->name('statistics');
        Route::get('/requiring-attention', [EmploymentContractsController::class, 'requiringAttention'])->name('requiring-attention');
        Route::post('/{contract}/upload-document', [EmploymentContractsController::class, 'uploadDocument'])->name('upload-document');
        Route::get('/{contract}/download-document/{documentType}', [EmploymentContractsController::class, 'downloadDocument'])->name('download-document');
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
    Route::prefix('training')->name('training.')->group(function () {
        Route::get('/', [App\Http\Controllers\TrainingController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\TrainingController::class, 'store'])->name('programs.store');
        Route::get('/plans', [App\Http\Controllers\TrainingPlanController::class, 'index'])->name('plans');
        Route::post('/plans', [App\Http\Controllers\TrainingPlanController::class, 'store'])->name('plans.store');
        Route::put('/plans/{plan}', [App\Http\Controllers\TrainingPlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{plan}', [App\Http\Controllers\TrainingPlanController::class, 'destroy'])->name('plans.destroy');
        Route::get('/completions', [App\Http\Controllers\TrainingController::class, 'completions'])->name('completions');
        Route::get('/certificate/{enrollment}', [App\Http\Controllers\TrainingController::class, 'certificate'])->name('certificate');
        Route::get('/programs/{program}', [App\Http\Controllers\TrainingController::class, 'show'])->name('programs.show');
        Route::put('/programs/{program}', [App\Http\Controllers\TrainingController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{program}', [App\Http\Controllers\TrainingController::class, 'destroy'])->name('programs.destroy');
        Route::post('/programs/{program}/sessions', [App\Http\Controllers\TrainingSessionController::class, 'store'])->name('sessions.store');
        Route::get('/sessions/{session}', [App\Http\Controllers\TrainingSessionController::class, 'show'])->name('sessions.show');
        Route::put('/sessions/{session}', [App\Http\Controllers\TrainingSessionController::class, 'update'])->name('sessions.update');
        Route::delete('/sessions/{session}', [App\Http\Controllers\TrainingSessionController::class, 'destroy'])->name('sessions.destroy');
        Route::post('/sessions/{session}/enroll', [App\Http\Controllers\TrainingSessionController::class, 'bulkEnroll'])->name('sessions.bulkEnroll');
        Route::patch('/enrollments/{enrollment}/attendance', [App\Http\Controllers\TrainingSessionController::class, 'updateAttendance'])->name('enrollments.attendance');
        Route::patch('/enrollments/{enrollment}/score', [App\Http\Controllers\TrainingSessionController::class, 'updateScore'])->name('enrollments.score');
        Route::delete('/enrollments/{enrollment}', [App\Http\Controllers\TrainingSessionController::class, 'unenroll'])->name('enrollments.unenroll');
    });

    // Benefits Routes
    Route::prefix('benefits')->name('benefits.')->group(function () {
        Route::get('/enrollment', [App\Http\Controllers\BenefitsController::class, 'enrollment'])->name('enrollment');
        Route::post('/enrollment', [App\Http\Controllers\BenefitsController::class, 'storeEnrollment'])->name('enrollment.store');
        Route::put('/enrollment/{id}', [App\Http\Controllers\BenefitsController::class, 'updateEnrollment'])->name('enrollment.update');
        Route::delete('/enrollment/{id}', [App\Http\Controllers\BenefitsController::class, 'destroyEnrollment'])->name('enrollment.destroy');
        Route::get('/life-events', [App\Http\Controllers\BenefitsController::class, 'lifeEvents'])->name('life-events');
        Route::post('/life-events', [App\Http\Controllers\BenefitsController::class, 'storeLifeEvent'])->name('life-events.store');
        Route::put('/life-events/{id}', [App\Http\Controllers\BenefitsController::class, 'updateLifeEvent'])->name('life-events.update');
        Route::delete('/life-events/{id}', [App\Http\Controllers\BenefitsController::class, 'destroyLifeEvent'])->name('life-events.destroy');
        Route::get('/plans', [App\Http\Controllers\BenefitsController::class, 'plans'])->name('plans');
        Route::post('/plans', [App\Http\Controllers\BenefitsController::class, 'storePlan'])->name('plans.store');
        Route::put('/plans/{id}', [App\Http\Controllers\BenefitsController::class, 'updatePlan'])->name('plans.update');
        Route::delete('/plans/{id}', [App\Http\Controllers\BenefitsController::class, 'destroyPlan'])->name('plans.destroy');
    });

    // Succession Routes
    Route::prefix('succession')->name('succession.')->group(function () {
        Route::get('/talent-pools', [App\Http\Controllers\SuccessionController::class, 'talentPools'])->name('talent-pools');
        Route::post('/talent-pools', [App\Http\Controllers\SuccessionController::class, 'storeTalentPool'])->name('talent-pools.store');
        Route::put('/talent-pools/{id}', [App\Http\Controllers\SuccessionController::class, 'updateTalentPool'])->name('talent-pools.update');
        Route::delete('/talent-pools/{id}', [App\Http\Controllers\SuccessionController::class, 'destroyTalentPool'])->name('talent-pools.destroy');
        Route::post('/talent-pools/{poolId}/members', [App\Http\Controllers\SuccessionController::class, 'storeMember'])->name('talent-pools.members.store');
        Route::patch('/members/{memberId}', [App\Http\Controllers\SuccessionController::class, 'updateMember'])->name('talent-pools.members.update');
        Route::delete('/members/{memberId}', [App\Http\Controllers\SuccessionController::class, 'destroyMember'])->name('talent-pools.members.destroy');
        Route::get('/readiness', [App\Http\Controllers\SuccessionController::class, 'readiness'])->name('readiness');
        Route::post('/readiness', [App\Http\Controllers\SuccessionController::class, 'storeReadiness'])->name('readiness.store');
        Route::put('/readiness/{id}', [App\Http\Controllers\SuccessionController::class, 'updateReadiness'])->name('readiness.update');
        Route::delete('/readiness/{id}', [App\Http\Controllers\SuccessionController::class, 'destroyReadiness'])->name('readiness.destroy');
        Route::get('/readiness/export', [App\Http\Controllers\SuccessionController::class, 'exportReadiness'])->name('readiness.export');
        Route::get('/career-paths', [App\Http\Controllers\SuccessionController::class, 'careerPaths'])->name('career-paths');
        Route::post('/career-paths', [App\Http\Controllers\SuccessionController::class, 'storeCareerPath'])->name('career-paths.store');
        Route::put('/career-paths/{id}', [App\Http\Controllers\SuccessionController::class, 'updateCareerPath'])->name('career-paths.update');
        Route::delete('/career-paths/{id}', [App\Http\Controllers\SuccessionController::class, 'destroyCareerPath'])->name('career-paths.destroy');
        Route::post('/career-paths/{pathId}/levels', [App\Http\Controllers\SuccessionController::class, 'storeLevel'])->name('career-paths.levels.store');
        Route::put('/levels/{levelId}', [App\Http\Controllers\SuccessionController::class, 'updateLevel'])->name('career-paths.levels.update');
        Route::delete('/levels/{levelId}', [App\Http\Controllers\SuccessionController::class, 'destroyLevel'])->name('career-paths.levels.destroy');
        Route::post('/career-paths/{pathId}/members', [App\Http\Controllers\SuccessionController::class, 'storePathMember'])->name('career-paths.members.store');
        Route::patch('/path-members/{memberId}', [App\Http\Controllers\SuccessionController::class, 'updatePathMember'])->name('career-paths.members.update');
        Route::delete('/path-members/{memberId}', [App\Http\Controllers\SuccessionController::class, 'destroyPathMember'])->name('career-paths.members.destroy');
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
        Route::get('/export', [\App\Http\Controllers\DepartmentsController::class, 'export'])->name('export');
        Route::get('/import-template', [\App\Http\Controllers\DepartmentsController::class, 'importTemplate'])->name('import-template');
        Route::post('/import', [\App\Http\Controllers\DepartmentsController::class, 'import'])->name('import');
        Route::post('/', [\App\Http\Controllers\DepartmentsController::class, 'store'])->name('store');
        Route::put('/{department}', [\App\Http\Controllers\DepartmentsController::class, 'update'])->name('update');
        Route::post('/{department}/toggle-status', [\App\Http\Controllers\DepartmentsController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{department}', [\App\Http\Controllers\DepartmentsController::class, 'destroy'])->name('destroy');
    });

    // Positions Routes
    Route::prefix('positions')->name('positions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PositionsController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\PositionsController::class, 'export'])->name('export');
        Route::get('/import-template', [\App\Http\Controllers\PositionsController::class, 'importTemplate'])->name('import-template');
        Route::post('/import', [\App\Http\Controllers\PositionsController::class, 'import'])->name('import');
        Route::post('/', [\App\Http\Controllers\PositionsController::class, 'store'])->name('store');
        Route::put('/{position}', [\App\Http\Controllers\PositionsController::class, 'update'])->name('update');
        Route::post('/{position}/toggle-status', [\App\Http\Controllers\PositionsController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{position}', [\App\Http\Controllers\PositionsController::class, 'destroy'])->name('destroy');
    });

    // Audit Trail Routes
    Route::prefix('audit-trail')->name('audit-trail.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AuditController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\AuditController::class, 'export'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\AuditController::class, 'show'])->name('show');
    });

    // Data Backup Routes
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [\App\Http\Controllers\BackupController::class, 'index'])->name('index')->middleware('permission:backups.view');
        Route::post('/', [\App\Http\Controllers\BackupController::class, 'create'])->name('create')->middleware('permission:backups.manage');
        Route::post('/upload', [\App\Http\Controllers\BackupController::class, 'upload'])->name('upload')->middleware('permission:backups.manage');
        Route::post('/clean', [\App\Http\Controllers\BackupController::class, 'clean'])->name('clean')->middleware('permission:backups.manage');
        Route::post('/{filename}/restore', [\App\Http\Controllers\BackupController::class, 'restore'])->name('restore')->middleware('permission:backups.manage');
        Route::get('/{filename}/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('download')->middleware('permission:backups.view');
        Route::delete('/{filename}', [\App\Http\Controllers\BackupController::class, 'destroy'])->name('destroy')->middleware('permission:backups.manage');
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
        Route::get('/export', [OnboardingController::class, 'exportReport'])->name('onboarding.export');
        Route::post('/start', [OnboardingController::class, 'startOnboarding'])->name('onboarding.start');
        Route::post('/complete/{employeeId}', [OnboardingController::class, 'completeOnboarding'])->name('onboarding.complete');
        Route::get('/progress/{employeeId}', [OnboardingController::class, 'getProgress'])->name('onboarding.progress');
        Route::post('/checklist/{checklistId}/toggle', [OnboardingController::class, 'toggleChecklistItem'])->name('onboarding.checklist.toggle');
        Route::get('/form-data', [OnboardingController::class, 'getFormData'])->name('onboarding.form-data');
        Route::get('/new-hires', [OnboardingController::class, 'getNewHires'])->name('onboarding.new-hires');
        Route::get('/checklist-template', [OnboardingController::class, 'getChecklistTemplate'])->name('onboarding.checklist-template.get');
        Route::post('/checklist-template', [OnboardingController::class, 'saveChecklistTemplate'])->name('onboarding.checklist-template.save');
        Route::post('/checklist-template/reset', [OnboardingController::class, 'resetChecklistTemplate'])->name('onboarding.checklist-template.reset');
        Route::post('/{employeeId}/documents', [OnboardingController::class, 'uploadDocument'])->name('onboarding.documents.upload');
        Route::post('/documents/{documentId}/verify', [OnboardingController::class, 'verifyDocument'])->name('onboarding.documents.verify');
        Route::delete('/documents/{documentId}', [OnboardingController::class, 'deleteDocument'])->name('onboarding.documents.delete');
        Route::get('/document-types', [OnboardingController::class, 'getRequiredDocumentTypes'])->name('onboarding.document-types');
        Route::post('/{employeeId}/contract/generate', [OnboardingController::class, 'generateContract'])->name('onboarding.contract.generate');
        Route::post('/contract/{contractId}/sign', [OnboardingController::class, 'signContract'])->name('onboarding.contract.sign');
        Route::get('/contract/{contractId}', [OnboardingController::class, 'getContractForSigning'])->name('onboarding.contract.view');
        Route::get('/policy-types', [OnboardingController::class, 'getPolicyTypes'])->name('onboarding.policy-types');
        Route::post('/{employeeId}/policies', [OnboardingController::class, 'assignPolicies'])->name('onboarding.policies.assign');
        Route::post('/policy/{acknowledgmentId}/acknowledge', [OnboardingController::class, 'acknowledgePolicy'])->name('onboarding.policy.acknowledge');
        Route::get('/{employeeId}/policies', [OnboardingController::class, 'getEmployeePolicies'])->name('onboarding.policies.list');
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
        Route::get('/create', [DocumentsController::class, 'create'])->name('documents.create');
        Route::post('/', [DocumentsController::class, 'store'])->name('documents.store');
        Route::get('/{id}/edit', [DocumentsController::class, 'edit'])->name('documents.edit');
        Route::put('/{id}', [DocumentsController::class, 'update'])->name('documents.update');
        Route::delete('/{id}', [DocumentsController::class, 'destroy'])->name('documents.destroy');
        Route::post('/preview', [DocumentsController::class, 'preview'])->name('documents.preview');
        Route::get('/file-preview/{id}', [DocumentsController::class, 'filePreview'])->name('documents.file-preview');
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
        })->name('clients.index')->middleware('permission:clients.view');
        
        Route::get('/create', function () {
            return view('clients.create');
        })->name('clients.create')->middleware('permission:clients.create');
        
        Route::get('/edit', function () {
            return view('clients.edit');
        })->name('clients.edit')->middleware('permission:clients.edit');
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
            Route::post('/switch', [ClientSwitchController::class, 'switch'])->middleware('auth');
            Route::get('/current', [ClientSwitchController::class, 'current'])->middleware('auth');
            Route::get('/available', [ClientSwitchController::class, 'available'])->middleware('auth');
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
    Route::get('/employees/positions-by-department/{departmentId}', [EmployeeController::class, 'getPositionsByDepartment'])->name('employees.positions-by-department');

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
    Route::get('/contracts/{contract}/download-pdf', [ContractController::class, 'downloadPdf'])->name('contracts.download-pdf');
    Route::get('/contracts/{contract}/print-pdf', [ContractController::class, 'printPdf'])->name('contracts.print-pdf');
    Route::get('/contracts/expiring', [ContractController::class, 'expiringSoon'])->name('contracts.expiring');
    Route::get('/contracts/statistics', [ContractController::class, 'statistics'])->name('contracts.statistics');
});
