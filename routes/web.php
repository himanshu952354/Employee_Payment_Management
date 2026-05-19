<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// 1. Guest Landing & Authentication
Route::get('/', [AuthController::class, 'showLanding'])->name('landing');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

// 2. Protected Routes (Requires Custom Session Auth)
Route::middleware(['admin.auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (Bifurcated: Admin/Boss Dashboard OR Employee Self-Service Dossier)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employee Directory (Admin only)
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Daily Attendance Sheet (Admin only)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // Payroll Clearing Desk (Admin only)
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll/generate', [PayrollController::class, 'generateMonth'])->name('payroll.generate');
    Route::put('/payroll/{payroll}/update', [PayrollController::class, 'updateSingle'])->name('payroll.update');
    Route::post('/payroll/{payroll}/pay', [PayrollController::class, 'processPayment'])->name('payroll.pay');

    // Printable Salary Slips (Admin or Employee - shared resource)
    Route::get('/payroll/{payroll}/slip', [PayrollController::class, 'slip'])->name('payroll.slip');

    // Chat System (Admin or Employee - shared resource)
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

    // Global Autocomplete Search
    Route::get('/search/query', [\App\Http\Controllers\SearchController::class, 'query'])->name('search.query');

    // Workspace & Account Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
});
