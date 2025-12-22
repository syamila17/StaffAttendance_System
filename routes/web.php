<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\StaffManagementController;
// use App\Http\Controllers\MetricsController;

// METRICS (Prometheus scraping) - Global
// Temporarily disabled due to database connection issues
// Route::get('/metrics', [MetricsController::class, 'index'])->name('metrics');

// DEBUG ROUTE - Check staff IDs in database
Route::get('/debug-staff', function() {
    $staffMembers = \App\Models\Staff::select('staff_id', 'staff_name', 'staff_email')->take(10)->get();
    return response()->json([
        'total_staff' => \App\Models\Staff::count(),
        'sample_staff' => $staffMembers,
        'columns' => \DB::getSchemaBuilder()->getColumnListing('staff')
    ]);
});

// ========================================
// ALL ROUTES (localhost:8000) - RAW LOCALHOST
// ========================================
Route::middleware('web')->group(function () {
    Route::get('/', fn() => redirect('/login'));

    // ========================================
    // STAFF ROUTES
    // ========================================
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('staff.logout');

    Route::middleware('staff.auth')->group(function () {
        Route::get('/staff_dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
        Route::get('/staff_profile', [StaffProfileController::class, 'show'])->name('staff.profile');
        Route::post('/staff_profile/update', [StaffProfileController::class, 'update'])->name('staff.profile.update');
        Route::get('/attendance', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkOut');
        Route::post('/attendance/update-status', [AttendanceController::class, 'updateStatus'])->name('attendance.updateStatus');
        Route::get('/staff/pie-chart-data', [StaffController::class, 'getPieChartData'])->name('staff.pieChartData');
        
        Route::get('/staff/apply-leave', [StaffController::class, 'showApplyLeave'])->name('staff.apply-leave');
        Route::post('/staff/leave', [StaffController::class, 'storeLeaveRequest'])->name('staff.leave.store');
        Route::get('/staff/leave-status', [StaffController::class, 'leaveStatus'])->name('staff.leave.status');
        Route::get('/staff/leave-notifications', [StaffController::class, 'getLeaveNotifications'])->name('staff.leave.notifications');
        Route::get('/staff/leave/{id}/download-proof', [StaffController::class, 'downloadProofFile'])->name('staff.leave.download-proof');
    });

    // ========================================
    // ADMIN ROUTES
    // ========================================
    Route::get('/admin_login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin_login', [AdminController::class, 'login']);
    Route::get('/admin_logout', [AdminController::class, 'logout'])->name('admin.logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/admin_dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance');
        Route::post('/admin/attendance/mark', [AdminAttendanceController::class, 'mark'])->name('admin.attendance.mark');
        Route::get('/admin/attendance/report', [AdminAttendanceController::class, 'report'])->name('admin.attendance.report');
        
        Route::get('/admin/staff', [StaffManagementController::class, 'index'])->name('admin.staff.index');
        Route::get('/admin/staff/create', [StaffManagementController::class, 'create'])->name('admin.staff.create');
        Route::post('/admin/staff', [StaffManagementController::class, 'store'])->name('admin.staff.store');
        Route::get('/admin/staff/{id}/edit', [StaffManagementController::class, 'edit'])->name('admin.staff.edit');
        Route::put('/admin/staff/{id}', [StaffManagementController::class, 'update'])->name('admin.staff.update');
        Route::delete('/admin/staff/{id}', [StaffManagementController::class, 'destroy'])->name('admin.staff.destroy');
        
        Route::get('/admin/departments', [AdminController::class, 'departments'])->name('admin.departments');
        
        Route::get('/admin/leave-requests', [AdminController::class, 'leaveRequests'])->name('admin.leave.requests');
        Route::post('/admin/leave/{id}/approve', [AdminController::class, 'approveLease'])->name('admin.leave.approve');
        Route::post('/admin/leave/{id}/reject', [AdminController::class, 'rejectLeave'])->name('admin.leave.reject');
        Route::get('/admin/leave-pending-count', [AdminController::class, 'getPendingLeaveCount'])->name('admin.leave.pending-count');
    });
});

