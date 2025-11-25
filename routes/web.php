<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\MetricsController;

// HOME
Route::get('/', fn() => redirect('/login'));

// METRICS (Prometheus scraping)
Route::get('/metrics', [MetricsController::class, 'index'])->name('metrics');

// STAFF LOGIN
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('staff.logout');

// ADMIN LOGIN  
Route::get('/admin_login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin_login', [AdminController::class, 'login']);
Route::get('/admin_logout', [AdminController::class, 'logout'])->name('admin.logout');

// PROTECTED STAFF ROUTES
Route::middleware('staff.auth')->group(function () {
    Route::get('/staff_dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/staff_profile', [StaffProfileController::class, 'show'])->name('staff.profile');
    Route::post('/staff_profile/update', [StaffProfileController::class, 'update'])->name('staff.profile.update');
    Route::get('/attendance', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkOut');
    Route::post('/attendance/update-status', [AttendanceController::class, 'updateStatus'])->name('attendance.updateStatus');
});

// PROTECTED ADMIN ROUTES
Route::middleware('admin.auth')->group(function () {
    Route::get('/admin_dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance');
    Route::post('/admin/attendance/mark', [AdminAttendanceController::class, 'mark'])->name('admin.attendance.mark');
    Route::get('/admin/attendance/report', [AdminAttendanceController::class, 'report'])->name('admin.attendance.report');
});