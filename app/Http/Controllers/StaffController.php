<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Attendance;
use Carbon\Carbon;


class StaffController extends Controller
{
    public function dashboard()
    {
        // ✅ No need to check here - middleware already verified staff_id exists
        $staffName = Session::get('staff_name');
        $staffEmail = Session::get('staff_email');
        $staffId = Session::get('staff_id');

        // Get today's attendance
        $today = Carbon::today();
        $todayAttendance = Attendance::where('staff_id', $staffId)
            ->whereDate('attendance_date', $today)
            ->first();

        // Get attendance statistics
        $totalPresent = Attendance::where('staff_id', $staffId)
            ->where('status', 'present')
            ->count();

        $totalAbsent = Attendance::where('staff_id', $staffId)
            ->where('status', 'absent')
            ->count();

        $totalLate = Attendance::where('staff_id', $staffId)
            ->where('status', 'late')
            ->count();

        // Get recent attendance history (last 30 records)
        $recentAttendance = Attendance::where('staff_id', $staffId)
            ->orderBy('attendance_date', 'desc')
            ->limit(30)
            ->get();

        return view('staff_dashboard', compact(
            'staffName', 
            'staffEmail',
            'todayAttendance',
            'totalPresent',
            'totalAbsent',
            'totalLate',
            'recentAttendance'
        ));
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['staff_id','staff_name','staff_email']);
        $request->session()->flush();

        return redirect('/login')->with('success', 'You have successfully logged out. ');
    }
}
