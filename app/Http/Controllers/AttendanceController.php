<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\StaffProfile;
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

        // Get profile image
        $profile = StaffProfile::where('staff_id', $staffId)->first();

        $today = Carbon::today();
        $todayAttendance = Attendance::where('staff_id', $staffId)
            ->whereDate('attendance_date', $today)
            ->first();

        $recentAttendance = Attendance::where('staff_id', $staffId)
            ->orderBy('attendance_date', 'desc')
            ->limit(30)
            ->get();

        return view('attendance', compact('profile', 'todayAttendance', 'recentAttendance'));
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

    /**
     * Update attendance status
     */
    public function updateStatus(Request $request)
    {
        $staffId = session('staff_id');
        if (!$staffId) {
            return back()->withErrors(['error' => 'Please login first']);
        }

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,el,on leave,half day',
            'date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:255'
        ]);

        $attendance = Attendance::firstOrCreate(
            [
                'staff_id' => $staffId,
                'attendance_date' => $validated['date']
            ],
            [
                'status' => $validated['status'],
                'check_in_time' => $validated['check_in_time'] ? $validated['check_in_time'] . ':00' : null,
                'check_out_time' => $validated['check_out_time'] ? $validated['check_out_time'] . ':00' : null,
                'remarks' => $validated['remarks'] ?? null
            ]
        );

        // If record exists, update it
        if ($attendance->wasRecentlyCreated === false) {
            $attendance->update([
                'status' => $validated['status'],
                'check_in_time' => $validated['check_in_time'] ? $validated['check_in_time'] . ':00' : $attendance->check_in_time,
                'check_out_time' => $validated['check_out_time'] ? $validated['check_out_time'] . ':00' : $attendance->check_out_time,
                'remarks' => $validated['remarks'] ?? $attendance->remarks
            ]);
        }

        return back()->with('success', 'Attendance status updated successfully!');
    }
}
