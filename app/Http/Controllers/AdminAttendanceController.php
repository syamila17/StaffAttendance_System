<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    /**
     * Show attendance management page
     */
    public function index()
    {
        // ✅ No need to check - middleware already verified admin_id exists
        $staff = Staff::all();
        $selectedDate = request('date', Carbon::today()->format('Y-m-d'));
        $attendanceData = Attendance::whereDate('attendance_date', $selectedDate)->get();

        $stats = [
            'total_staff' => $staff->count(),
            'present' => $attendanceData->where('status', 'present')->count(),
            'absent' => $attendanceData->where('status', 'absent')->count(),
            'late' => $attendanceData->where('status', 'late')->count(),
            'leave' => $attendanceData->where('status', 'leave')->count(),
        ];

        return view('admin.attendance', compact('staff', 'attendanceData', 'selectedDate', 'stats'));
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
    public function report()
    {
        // ✅ No need to check - middleware already verified admin_id exists
        $startDate = request('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', Carbon::today()->format('Y-m-d'));
        $staffId = request('staff_id');

        $query = Attendance::whereBetween('attendance_date', [$startDate, $endDate]);
        
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $attendanceRecords = $query->orderBy('attendance_date', 'desc')->get();
        $staff = Staff::all();

        $summary = [
            'total_records' => $attendanceRecords->count(),
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'late' => $attendanceRecords->where('status', 'late')->count(),
            'leave' => $attendanceRecords->where('status', 'leave')->count(),
        ];

        return view('admin.attendance-report', compact('attendanceRecords', 'staff', 'startDate', 'endDate', 'summary'));
    }
}
