<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Organization Routes
    Route::prefix('organization')->group(function () {
        Route::get('/setup', function () {
            return view('organization.setup');
        })->name('organization.setup');
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

    // Employee Self Service Routes
    Route::prefix('selfservice')->group(function () {
        Route::get('/', function () {
            return view('selfservice.index');
        })->name('selfservice.index');
    });

    // Case Management Routes
    Route::prefix('casemanagement')->group(function () {
        Route::get('/', function () {
            return view('casemanagement.index');
        })->name('casemanagement.index');
    });

    // Admin Routes (Super Admin and HR Admin only)
    Route::prefix('users')->group(function () {
        Route::get('/', function () {
            return view('users.index');
        })->name('users.index');
    });

    Route::prefix('clients')->group(function () {
        Route::get('/', function () {
            return view('clients.index');
        })->name('clients.index');
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
