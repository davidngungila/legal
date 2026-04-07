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
Route::middleware(['web', 'auth', \App\Http\Middleware\ShareCurrentUser::class])->group(function () {
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
        Route::get('/', function () {
            return view('payroll.index');
        })->name('payroll.index');
        Route::get('/payslip', function () {
            return view('payroll.payslip');
        })->name('payroll.payslip');
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
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');

    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings');

    Route::get('/help', function () {
        return view('help.index');
    })->name('help');
});

// Default route - redirect to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});
