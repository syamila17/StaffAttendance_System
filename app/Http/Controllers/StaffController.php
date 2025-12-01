<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Attendance;
use App\Models\StaffProfile;
use App\Models\LeaveRequest;
use Carbon\Carbon;


class StaffController extends Controller
{
    public function dashboard()
    {
        // ✅ No need to check here - middleware already verified staff_id exists
        $staffName = Session::get('staff_name');
        $staffEmail = Session::get('staff_email');
        $staffId = Session::get('staff_id');

        // Get profile image
        $profile = StaffProfile::where('staff_id', $staffId)->first();

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
            'profile',
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

    public function showApplyLeave()
    {
        $staffId = Session::get('staff_id');
        $staffName = Session::get('staff_name');

        // Get profile image
        $profile = StaffProfile::where('staff_id', $staffId)->first();

        return view('staff_apply_leave', compact('staffName', 'profile'));
    }

    public function storeLeaveRequest(Request $request)
    {
        $staffId = Session::get('staff_id');

        // Validate the request
        $validated = $request->validate([
            'leave_type' => 'required|string|in:Annual Leave,Sick Leave,Emergency Leave,Personal Leave,Compassionate Leave,Other',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'nullable|string|max:1000',
        ]);

        // Create the leave request
        LeaveRequest::create([
            'staff_id' => $staffId,
            'leave_type' => $validated['leave_type'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('staff.apply-leave')->with('success', 'Leave request submitted successfully! The admin will review it soon.');
    }

    public function leaveStatus()
    {
        $staffId = Session::get('staff_id');
        $staffName = Session::get('staff_name');

        // Get profile image
        $profile = StaffProfile::where('staff_id', $staffId)->first();

        // Mark all status updates as viewed when the staff visits this page
        LeaveRequest::where('staff_id', $staffId)
            ->where('status_viewed', false)
            ->where(function($query) {
                $query->whereNotNull('approved_at')
                    ->orWhereNotNull('rejected_at');
            })
            ->update([
                'status_viewed' => true,
                'status_viewed_at' => Carbon::now()
            ]);

        // Get all leave requests
        $allLeaves = LeaveRequest::where('staff_id', $staffId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Count by status
        $pendingCount = $allLeaves->where('status', 'pending')->count();
        $approvedCount = $allLeaves->where('status', 'approved')->count();
        $rejectedCount = $allLeaves->where('status', 'rejected')->count();
        $totalRequests = $allLeaves->count();

        // Filter based on request parameter
        $filter = request('filter', 'all');
        if ($filter === 'all') {
            $filteredLeaves = $allLeaves;
        } else {
            $filteredLeaves = $allLeaves->where('status', $filter);
        }

        // Calculate off days for current month
        $currentMonth = date('F Y');
        $currentYear = date('Y');
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $offDaysThisMonth = 0;
        foreach ($allLeaves->where('status', 'approved') as $leave) {
            if ($leave->from_date <= $currentMonthEnd && $leave->to_date >= $currentMonthStart) {
                $daysInMonth = 0;
                $start = max($leave->from_date, $currentMonthStart);
                $end = min($leave->to_date, $currentMonthEnd);
                $daysInMonth = $start->diffInDays($end) + 1;
                $offDaysThisMonth += $daysInMonth;
            }
        }

        // Calculate annual leave statistics
        $totalAnnualLeave = 20; // Default annual leave (can be fetched from staff table if stored there)
        $usedLeave = 0;
        $currentYearStart = Carbon::create($currentYear, 1, 1);
        $currentYearEnd = Carbon::create($currentYear, 12, 31);

        foreach ($allLeaves->where('status', 'approved') as $leave) {
            if ($leave->leave_type === 'Annual Leave' && $leave->from_date <= $currentYearEnd && $leave->to_date >= $currentYearStart) {
                $daysUsed = 0;
                $start = max($leave->from_date, $currentYearStart);
                $end = min($leave->to_date, $currentYearEnd);
                $daysUsed = $start->diffInDays($end) + 1;
                $usedLeave += $daysUsed;
            }
        }

        $remainingBalance = $totalAnnualLeave - $usedLeave;

        return view('staff_status_leave', [
            'staffName' => $staffName,
            'profile' => $profile,
            'filteredLeaves' => $filteredLeaves,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'totalRequests' => $totalRequests,
            'currentMonth' => $currentMonth,
            'totalOffDaysMonth' => $offDaysThisMonth,
            'totalAnnualLeave' => $totalAnnualLeave,
            'usedLeave' => $usedLeave,
            'remainingBalance' => $remainingBalance
        ]);
    }

    public function getLeaveNotifications()
    {
        $staffId = Session::get('staff_id');

        // Get recent unviewed leave status changes (approved or rejected)
        $notifications = LeaveRequest::where('staff_id', $staffId)
            ->where('status_viewed', false)
            ->where(function ($query) {
                $query->whereNotNull('approved_at')
                    ->orWhereNotNull('rejected_at');
            })
            ->where(function ($query) {
                $query->where('approved_at', '>=', Carbon::now()->subDays(7))
                    ->orWhere('rejected_at', '>=', Carbon::now()->subDays(7));
            })
            ->orderBy(function ($query) {
                $query->selectRaw('GREATEST(COALESCE(approved_at, 0), COALESCE(rejected_at, 0))');
            }, 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications->map(function ($leave) {
                return [
                    'id' => $leave->leave_request_id,
                    'type' => $leave->status,
                    'leave_type' => $leave->leave_type,
                    'from_date' => $leave->from_date->format('M d, Y'),
                    'to_date' => $leave->to_date->format('M d, Y'),
                    'message' => 'Your ' . strtolower($leave->leave_type) . ' request has been ' . $leave->status,
                    'timestamp' => $leave->status === 'approved' ? $leave->approved_at->diffForHumans() : $leave->rejected_at->diffForHumans(),
                    'url' => route('staff.leave.status')
                ];
            })
        ]);
    }
}
