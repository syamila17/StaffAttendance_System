<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Response;

class MetricsController extends Controller
{
    /**
     * Expose metrics in Prometheus format
     */
    public function index()
    {
        try {
            // Test database connection
            \Illuminate\Support\Facades\DB::connection()->getPdo();
        } catch (\Exception $e) {
            // Return empty metrics if database is unavailable
            return response("# Database connection unavailable\n", 503, ['Content-Type' => 'text/plain']);
        }
        
        $today = Carbon::today();
        
        // Get attendance statistics for today
        $presentToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'present')
            ->count();
            
        $absentToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'absent')
            ->count();
            
        $lateToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'late')
            ->count();
            
        $elToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'el')
            ->count();
            
        $leaveToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'on leave')
            ->count();
            
        $halfDayToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'half day')
            ->count();

        // Get total staff
        $totalStaff = Attendance::whereDate('attendance_date', $today)
            ->distinct('staff_id')
            ->count('staff_id');

        // Get historical data (last 7 days)
        $historicalData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $present = Attendance::whereDate('attendance_date', $date)
                ->where('status', 'present')
                ->count();
            $absent = Attendance::whereDate('attendance_date', $date)
                ->where('status', 'absent')
                ->count();
            $late = Attendance::whereDate('attendance_date', $date)
                ->where('status', 'late')
                ->count();
            
            $historicalData[] = [
                'date' => $date->timestamp * 1000,
                'present' => $present,
                'absent' => $absent,
                'late' => $late
            ];
        }

        // Get leave statistics
        $currentYear = date('Y');
        $currentYearStart = Carbon::create($currentYear, 1, 1);
        $currentYearEnd = Carbon::create($currentYear, 12, 31);

        $allStaff = Staff::all();
        $totalLeaveBalance = 0;
        $totalLeaveUsed = 0;
        $totalLeaveRemaining = 0;
        
        foreach ($allStaff as $staff) {
            $totalAnnualLeave = 20;
            $usedLeave = 0;
            
            $leaveRequests = LeaveRequest::where('staff_id', $staff->staff_id)
                ->where('status', 'approved')
                ->get();
            
            foreach ($leaveRequests as $leave) {
                if ($leave->leave_type === 'Annual Leave' && 
                    $leave->from_date <= $currentYearEnd && 
                    $leave->to_date >= $currentYearStart) {
                    $start = max($leave->from_date, $currentYearStart);
                    $end = min($leave->to_date, $currentYearEnd);
                    $daysUsed = $start->diffInDays($end) + 1;
                    $usedLeave += $daysUsed;
                }
            }
            
            $remainingBalance = $totalAnnualLeave - $usedLeave;
            $totalLeaveBalance += $totalAnnualLeave;
            $totalLeaveUsed += $usedLeave;
            $totalLeaveRemaining += $remainingBalance;
        }

        // Pending leave requests
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
        $approvedLeaves = LeaveRequest::where('status', 'approved')->count();
        $rejectedLeaves = LeaveRequest::where('status', 'rejected')->count();

        // Build Prometheus metrics format
        $metrics = "# HELP attendance_present_today Total staff present today\n";
        $metrics .= "# TYPE attendance_present_today gauge\n";
        $metrics .= "attendance_present_today {job=\"laravel-app\"} $presentToday\n\n";

        $metrics .= "# HELP attendance_absent_today Total staff absent today\n";
        $metrics .= "# TYPE attendance_absent_today gauge\n";
        $metrics .= "attendance_absent_today {job=\"laravel-app\"} $absentToday\n\n";

        $metrics .= "# HELP attendance_late_today Total staff late today\n";
        $metrics .= "# TYPE attendance_late_today gauge\n";
        $metrics .= "attendance_late_today {job=\"laravel-app\"} $lateToday\n\n";

        $metrics .= "# HELP attendance_el_today Total staff on EL today\n";
        $metrics .= "# TYPE attendance_el_today gauge\n";
        $metrics .= "attendance_el_today {job=\"laravel-app\"} $elToday\n\n";

        $metrics .= "# HELP attendance_leave_today Total staff on leave today\n";
        $metrics .= "# TYPE attendance_leave_today gauge\n";
        $metrics .= "attendance_leave_today {job=\"laravel-app\"} $leaveToday\n\n";

        $metrics .= "# HELP attendance_halfday_today Total staff half day today\n";
        $metrics .= "# TYPE attendance_halfday_today gauge\n";
        $metrics .= "attendance_halfday_today {job=\"laravel-app\"} $halfDayToday\n\n";

        $metrics .= "# HELP attendance_total_staff Total staff with attendance records today\n";
        $metrics .= "# TYPE attendance_total_staff gauge\n";
        $metrics .= "attendance_total_staff {job=\"laravel-app\"} $totalStaff\n\n";

        // Status breakdown
        $metrics .= "# HELP attendance_by_status Attendance breakdown by status\n";
        $metrics .= "# TYPE attendance_by_status gauge\n";
        $metrics .= "attendance_by_status {job=\"laravel-app\",status=\"present\"} $presentToday\n";
        $metrics .= "attendance_by_status {job=\"laravel-app\",status=\"absent\"} $absentToday\n";
        $metrics .= "attendance_by_status {job=\"laravel-app\",status=\"late\"} $lateToday\n";
        $metrics .= "attendance_by_status {job=\"laravel-app\",status=\"el\"} $elToday\n";
        $metrics .= "attendance_by_status {job=\"laravel-app\",status=\"on_leave\"} $leaveToday\n";
        $metrics .= "attendance_by_status {job=\"laravel-app\",status=\"half_day\"} $halfDayToday\n\n";

        // Leave balance metrics
        $metrics .= "# HELP leave_total_balance Total annual leave balance for all staff\n";
        $metrics .= "# TYPE leave_total_balance gauge\n";
        $metrics .= "leave_total_balance {job=\"laravel-app\"} $totalLeaveBalance\n\n";

        $metrics .= "# HELP leave_used Total annual leave used\n";
        $metrics .= "# TYPE leave_used gauge\n";
        $metrics .= "leave_used {job=\"laravel-app\"} $totalLeaveUsed\n\n";

        $metrics .= "# HELP leave_remaining Total annual leave remaining\n";
        $metrics .= "# TYPE leave_remaining gauge\n";
        $metrics .= "leave_remaining {job=\"laravel-app\"} $totalLeaveRemaining\n\n";

        $metrics .= "# HELP leave_requests_pending Pending leave requests\n";
        $metrics .= "# TYPE leave_requests_pending gauge\n";
        $metrics .= "leave_requests_pending {job=\"laravel-app\"} $pendingLeaves\n\n";

        $metrics .= "# HELP leave_requests_approved Approved leave requests\n";
        $metrics .= "# TYPE leave_requests_approved gauge\n";
        $metrics .= "leave_requests_approved {job=\"laravel-app\"} $approvedLeaves\n\n";

        $metrics .= "# HELP leave_requests_rejected Rejected leave requests\n";
        $metrics .= "# TYPE leave_requests_rejected gauge\n";
        $metrics .= "leave_requests_rejected {job=\"laravel-app\"} $rejectedLeaves\n\n";

        // Daily metrics for last 7 days
        $metrics .= "# HELP attendance_daily_present Daily present count\n";
        $metrics .= "# TYPE attendance_daily_present gauge\n";
        foreach ($historicalData as $data) {
            $metrics .= "attendance_daily_present {job=\"laravel-app\"} {$data['present']} {$data['date']}\n";
        }
        $metrics .= "\n";

        $metrics .= "# HELP attendance_daily_absent Daily absent count\n";
        $metrics .= "# TYPE attendance_daily_absent gauge\n";
        foreach ($historicalData as $data) {
            $metrics .= "attendance_daily_absent {job=\"laravel-app\"} {$data['absent']} {$data['date']}\n";
        }
        $metrics .= "\n";

        $metrics .= "# HELP attendance_daily_late Daily late count\n";
        $metrics .= "# TYPE attendance_daily_late gauge\n";
        foreach ($historicalData as $data) {
            $metrics .= "attendance_daily_late {job=\"laravel-app\"} {$data['late']} {$data['date']}\n";
        }

        return response($metrics, 200, ['Content-Type' => 'text/plain']);
    }
}
