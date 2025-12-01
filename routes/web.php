<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\StaffManagementController;
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
    
    // Leave Request Routes
    Route::get('/staff/apply-leave', [StaffController::class, 'showApplyLeave'])->name('staff.apply-leave');
    Route::post('/staff/leave', [StaffController::class, 'storeLeaveRequest'])->name('staff.leave.store');
    Route::get('/staff/leave-status', [StaffController::class, 'leaveStatus'])->name('staff.leave.status');
    Route::get('/staff/leave-notifications', [StaffController::class, 'getLeaveNotifications'])->name('staff.leave.notifications');
});

// PROTECTED ADMIN ROUTES
Route::middleware('admin.auth')->group(function () {
    Route::get('/admin_dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance');
    Route::post('/admin/attendance/mark', [AdminAttendanceController::class, 'mark'])->name('admin.attendance.mark');
    Route::get('/admin/attendance/report', [AdminAttendanceController::class, 'report'])->name('admin.attendance.report');
    
    // Staff Management Routes
    Route::get('/admin/staff', [StaffManagementController::class, 'index'])->name('admin.staff.index');
    Route::get('/admin/staff/create', [StaffManagementController::class, 'create'])->name('admin.staff.create');
    Route::post('/admin/staff', [StaffManagementController::class, 'store'])->name('admin.staff.store');
    Route::get('/admin/staff/{id}/edit', [StaffManagementController::class, 'edit'])->name('admin.staff.edit');
    Route::put('/admin/staff/{id}', [StaffManagementController::class, 'update'])->name('admin.staff.update');
    Route::delete('/admin/staff/{id}', [StaffManagementController::class, 'destroy'])->name('admin.staff.destroy');
    
    // Department Routes
    Route::get('/admin/departments', [AdminController::class, 'departments'])->name('admin.departments');
    
    // Leave Request Routes
    Route::get('/admin/leave-requests', [AdminController::class, 'leaveRequests'])->name('admin.leave.requests');
    Route::post('/admin/leave/{id}/approve', [AdminController::class, 'approveLease'])->name('admin.leave.approve');
    Route::post('/admin/leave/{id}/reject', [AdminController::class, 'rejectLeave'])->name('admin.leave.reject');
    Route::get('/admin/leave-pending-count', [AdminController::class, 'getPendingLeaveCount'])->name('admin.leave.pending-count');
});