<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    /**
     * Show attendance management page
     */
    public function index(Request $request)
    {
        // Handle language switching
        if ($request->has('lang')) {
            $lang = $request->query('lang');
            if (in_array($lang, ['en', 'ms'])) {
                app()->setLocale($lang);
                session(['locale' => $lang]);
            }
        } else if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        // ✅ No need to check - middleware already verified admin_id exists
        $staff = Staff::all();
        $selectedDate = request('date', Carbon::today()->format('Y-m-d'));
        $attendanceData = Attendance::whereDate('attendance_date', $selectedDate)->get();

        // Auto-update attendance based on approved leave requests for the selected date
        $this->autoUpdateLeaveAttendance($selectedDate);
        
        // Re-fetch attendance data after auto-update
        $attendanceData = Attendance::whereDate('attendance_date', $selectedDate)->get();

        // Calculate stats excluding staff on leave
        $allStaff = $staff->count();
        $presentCount = $attendanceData->where('status', 'present')->count();
        $absentCount = $attendanceData->where('status', 'absent')->count();
        $lateCount = $attendanceData->where('status', 'late')->count();
        $leaveCount = $attendanceData->where('status', 'leave')->count();
        
        // Actual working count (present + late) = actual attendance
        $actualAttendance = $presentCount + $lateCount;

        $stats = [
            'total_staff' => $allStaff,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'leave' => $leaveCount,
            'actual_attendance' => $actualAttendance,
        ];

        return view('admin.attendance', compact('staff', 'attendanceData', 'selectedDate', 'stats'));
    }

    /**
     * Auto-update attendance based on approved leave requests
     */
    private function autoUpdateLeaveAttendance($date)
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        
        // Get all approved leave requests that cover this date
        $leaveRequests = LeaveRequest::where('status', 'approved')
            ->whereDate('from_date', '<=', $carbonDate)
            ->whereDate('to_date', '>=', $carbonDate)
            ->get();

        foreach ($leaveRequests as $leave) {
            // Check if attendance record exists for this staff on this date
            $attendance = Attendance::where('staff_id', $leave->staff_id)
                ->whereDate('attendance_date', $carbonDate)
                ->first();

            if ($attendance) {
                // Update to 'leave' status
                if ($attendance->status !== 'leave') {
                    $attendance->update([
                        'status' => 'leave',
                        'remarks' => $leave->leave_type . ' (Approved on ' . $leave->from_date->format('M d') . '-' . $leave->to_date->format('M d, Y') . ')'
                    ]);
                }
            } else {
                // Create new attendance record with 'leave' status
                Attendance::create([
                    'staff_id' => $leave->staff_id,
                    'attendance_date' => $carbonDate,
                    'status' => 'leave',
                    'remarks' => $leave->leave_type . ' (Approved)'
                ]);
            }
        }
    }

    /**
     * Mark attendance for a staff member
     */
    public function mark(Request $request)
    {
        // ✅ No need to check - middleware already verified admin_id exists
        $request->validate([
            'staff_id' => 'required|exists:staff,staff_id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,late,leave',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
        ]);

        Attendance::updateOrCreate(
            [
                'staff_id' => $request->staff_id,
                'attendance_date' => $request->attendance_date,
            ],
            [
                'status' => $request->status,
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'remarks' => $request->remarks ?? null,
            ]
        );

        return back()->with('success', 'Attendance marked successfully');
    }

    /**
     * Get attendance report
     */
    public function report(Request $request)
    {
        // Handle language switching
        if ($request->has('lang')) {
            $lang = $request->query('lang');
            if (in_array($lang, ['en', 'ms'])) {
                app()->setLocale($lang);
                session(['locale' => $lang]);
            }
        } else if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        // ✅ No need to check - middleware already verified admin_id exists
        $startDate = request('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', Carbon::today()->format('Y-m-d'));
        $staffId = request('staff_id');

        // Auto-update attendance for all dates in range based on leave requests
        $this->autoUpdateLeaveAttendanceRange($startDate, $endDate);

        $query = Attendance::whereBetween('attendance_date', [$startDate, $endDate]);
        
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $attendanceRecords = $query->orderBy('attendance_date', 'desc')->get();
        $staff = Staff::all();

        // Calculate stats excluding leave
        $totalRecords = $attendanceRecords->count();
        $presentCount = $attendanceRecords->where('status', 'present')->count();
        $absentCount = $attendanceRecords->where('status', 'absent')->count();
        $lateCount = $attendanceRecords->where('status', 'late')->count();
        $leaveCount = $attendanceRecords->where('status', 'leave')->count();
        
        // Actual attendance = present + late (excluding leave)
        $actualAttendance = $presentCount + $lateCount;

        $summary = [
            'total_records' => $totalRecords,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'leave' => $leaveCount,
            'actual_attendance' => $actualAttendance,
        ];

        return view('admin.attendance-report', compact('attendanceRecords', 'staff', 'startDate', 'endDate', 'summary'));
    }

    /**
     * Auto-update attendance based on approved leave requests for a date range
     */
    private function autoUpdateLeaveAttendanceRange($startDateStr, $endDateStr)
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $startDateStr)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $endDateStr)->endOfDay();
        
        // Get all approved leave requests that overlap with this date range
        $leaveRequests = LeaveRequest::where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('from_date', [$startDate, $endDate])
                      ->orWhereBetween('to_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('from_date', '<', $startDate)
                            ->where('to_date', '>', $endDate);
                      });
            })
            ->get();

        foreach ($leaveRequests as $leave) {
            // Calculate dates in range
            $loopStart = max($leave->from_date, $startDate);
            $loopEnd = min($leave->to_date, $endDate);
            
            // Update or create for each day in the leave period
            while ($loopStart <= $loopEnd) {
                $attendance = Attendance::where('staff_id', $leave->staff_id)
                    ->whereDate('attendance_date', $loopStart)
                    ->first();

                if ($attendance && $attendance->status !== 'leave') {
                    $attendance->update([
                        'status' => 'leave',
                        'remarks' => $leave->leave_type . ' (Approved)'
                    ]);
                } elseif (!$attendance) {
                    Attendance::create([
                        'staff_id' => $leave->staff_id,
                        'attendance_date' => $loopStart,
                        'status' => 'leave',
                        'remarks' => $leave->leave_type . ' (Approved)'
                    ]);
                }

                $loopStart->addDay();
            }
        }
    }
}
