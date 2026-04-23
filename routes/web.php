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
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PayrollController;

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
    Route::get('/sample-users', function () {
        return view('auth.sample-users');
    })->name('sample-users');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.post');
});
Route::middleware(['web'])->post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware(['web', 'auth', \App\Http\Middleware\ShareCurrentUser::class, \App\Http\Middleware\SetCurrentClient::class, \App\Http\Middleware\FilterByCurrentClient::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Organization Routes
    Route::prefix('organization')->group(function () {
        Route::get('/setup', [OrganizationController::class, 'setup'])->name('organization.setup');
        Route::post('/update', [OrganizationController::class, 'update'])->name('organization.update');
        Route::get('/stats', [OrganizationController::class, 'stats'])->name('organization.stats');
    });

    // User Management Routes
    Route::prefix('users')->group(function () {
        Route::get('/', function () {
            return view('users.index');
        })->name('users.index');
        
        Route::get('/create', function () {
            return view('users.create');
        })->name('users.create');
    });

    // Role Management Routes
    Route::prefix('roles')->group(function () {
        Route::get('/', function () {
            return view('roles.index');
        })->name('roles.index');
        
        Route::get('/create', function () {
            return view('roles.create');
        })->name('roles.create');
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

    // Employee Management Routes
    Route::prefix('employees')->group(function () {
        Route::get('/', function () {
            return view('employees.index');
        })->name('employees.index');
    });

    Route::prefix('recruitment')->group(function () {
        Route::get('/', function () {
            return view('recruitment.index');
        })->name('recruitment.index');
    });

    Route::prefix('onboarding')->group(function () {
        Route::get('/', function () {
            return view('onboarding.index');
        })->name('onboarding.index');
    });

    // Time & Attendance Routes
    Route::prefix('attendance')->group(function () {
        Route::get('/', function () {
            return view('attendance.index');
        })->name('attendance.index');
    });

    // Payroll Routes
    Route::prefix('payroll')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/upload', [PayrollController::class, 'showUploadForm'])->name('payroll.upload');
        Route::post('/upload', [PayrollController::class, 'uploadCsv'])->name('payroll.upload.csv');
        Route::get('/template', [PayrollController::class, 'downloadTemplate'])->name('payroll.template');
        Route::get('/payslip', function () {
            return view('payroll.payslip');
        })->name('payroll.payslip');
        Route::get('/{id}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::put('/{id}/status', [PayrollController::class, 'updateStatus'])->name('payroll.update.status');
        Route::delete('/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    });

    Route::prefix('compensation')->group(function () {
        Route::get('/', function () {
            return view('compensation.index');
        })->name('compensation.index');
    });

    // Performance Routes
    Route::prefix('performance')->group(function () {
        Route::get('/', function () {
            return view('performance.index');
        })->name('performance.index');
    });

    // Employee Relations & Discipline Routes
    Route::prefix('discipline')->group(function () {
        Route::get('/', function () {
            return view('discipline.index');
        })->name('discipline.index');
    });

    // Compliance Routes
    Route::prefix('compliance')->group(function () {
        Route::get('/', function () {
            return view('compliance.index');
        })->name('compliance.index');
    });

    // Training Routes
    Route::prefix('training')->group(function () {
        Route::get('/', function () {
            return view('training.index');
        })->name('training.index');
    });

    // Analytics Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/', function () {
            return view('analytics.index');
        })->name('analytics.index');
    });

    // Attendance Routes
    Route::prefix('attendance')->group(function () {
        Route::get('/', function () {
            return view('attendance.index');
        })->name('attendance.index');
    });

    // Compensation Routes
    Route::prefix('compensation')->group(function () {
        Route::get('/', function () {
            return view('compensation.index');
        })->name('compensation.index');
    });

    // Compliance Routes
    Route::prefix('compliance')->group(function () {
        Route::get('/', function () {
            return view('compliance.index');
        })->name('compliance.index');
    });

    // Discipline Routes
    Route::prefix('discipline')->group(function () {
        Route::get('/', function () {
            return view('discipline.index');
        })->name('discipline.index');
    });

    // Employees Routes
    Route::prefix('employees')->group(function () {
        Route::get('/', function () {
            return view('employees.index');
        })->name('employees.index');
    });

    // Onboarding Routes
    Route::prefix('onboarding')->group(function () {
        Route::get('/', [OnboardingController::class, 'index'])->name('onboarding.index');
        Route::post('/start', [OnboardingController::class, 'startOnboarding'])->name('onboarding.start');
        Route::post('/complete/{employeeId}', [OnboardingController::class, 'completeOnboarding'])->name('onboarding.complete');
        Route::get('/progress/{employeeId}', [OnboardingController::class, 'getProgress'])->name('onboarding.progress');
    });

    // Organization Routes
    Route::prefix('organization')->group(function () {
        Route::get('/', function () {
            return view('organization.index');
        })->name('organization.index');
    });

    // Payroll Routes
    Route::prefix('payroll')->group(function () {
        Route::get('/', function () {
            return view('payroll.index');
        })->name('payroll.index');
    });

    // Performance Routes
    Route::prefix('performance')->group(function () {
        Route::get('/', function () {
            return view('performance.index');
        })->name('performance.index');
    });

    // Recruitment Routes
    Route::prefix('recruitment')->group(function () {
        Route::get('/', function () {
            return view('recruitment.index');
        })->name('recruitment.index');
    });

    // Training Routes
    Route::prefix('training')->group(function () {
        Route::get('/', function () {
            return view('training.index');
        })->name('training.index');
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
        Route::get('/', function () {
            return view('casemanagement.index');
        })->name('casemanagement.index');
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

    // Admin Routes (Super Admin and HR Admin only)
    Route::prefix('users')->group(function () {
        Route::get('/', function () {
            return view('users.index');
        })->name('users.index');
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
    Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('/settings/privacy', [SettingsController::class, 'updatePrivacy'])->name('settings.privacy');
    Route::post('/settings/appearance', [SettingsController::class, 'updateAppearance'])->name('settings.appearance');
    Route::post('/settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.security');
    Route::post('/settings/data', [SettingsController::class, 'updateDataStorage'])->name('settings.data');
    Route::post('/settings/integrations', [SettingsController::class, 'updateIntegrations'])->name('settings.integrations');
    Route::post('/settings/reset', [SettingsController::class, 'resetToDefault'])->name('settings.reset');
    Route::get('/settings/export', [SettingsController::class, 'export'])->name('settings.export');
    Route::get('/settings/data', [SettingsController::class, 'getSettings'])->name('settings.data');

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

// Default route - redirect to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});
