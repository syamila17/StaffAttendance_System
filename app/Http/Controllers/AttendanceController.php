<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Show attendance page
     */
    public function show()
    {
        // ✅ No need to check - middleware already verified staff_id exists
        $staffId = session('staff_id');

        $today = Carbon::today();
        $todayAttendance = Attendance::where('staff_id', $staffId)
            ->whereDate('attendance_date', $today)
            ->first();

        $recentAttendance = Attendance::where('staff_id', $staffId)
            ->orderBy('attendance_date', 'desc')
            ->limit(30)
            ->get();

        return view('attendance', compact('todayAttendance', 'recentAttendance'));
    }

    /**
     * Check-in
     */
    public function checkIn()
    {
        $staffId = session('staff_id');
        if (!$staffId) {
            return back()->withErrors(['error' => 'Please login first']);
        }

        $today = Carbon::today();
        $attendance = Attendance::firstOrCreate(
            [
                'staff_id' => $staffId,
                'attendance_date' => $today
            ],
            [
                'status' => 'present',
                'check_in_time' => Carbon::now()->format('H:i:s')
            ]
        );

        if ($attendance->check_in_time === null) {
            $attendance->update(['check_in_time' => Carbon::now()->format('H:i:s')]);
        }

        return back()->with('success', 'Check-in successful!');
    }

    /**
     * Check-out
     */
    public function checkOut()
    {
        $staffId = session('staff_id');
        if (!$staffId) {
            return back()->withErrors(['error' => 'Please login first']);
        }

        $today = Carbon::today();
        $attendance = Attendance::where('staff_id', $staffId)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance) {
            return back()->withErrors(['error' => 'Please check-in first']);
        }

        $attendance->update(['check_out_time' => Carbon::now()->format('H:i:s')]);

        return back()->with('success', 'Check-out successful!');
    }
}
