<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Department;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showLoginForm(Request $request)
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

        return view('admin_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('admin_email', $request->email)->first();
        
        if (!$admin) {
            return back()->withInput()->withErrors(['email' => 'Admin email not found.']);
        }

        if (!Hash::check($request->password, $admin->admin_password)) {
            return back()->withInput()->withErrors(['password' => 'Incorrect password.']);
        }

        // Ensure session is started
        if (!session()->isStarted()) {
            session()->start();
        }
        
        // Store session data
        Session::put('admin_id', $admin->admin_id);
        Session::put('admin_name', $admin->admin_name);
        Session::put('admin_email', $admin->admin_email);
        Session::put('login_time', now()->timestamp);
        
        // Save session to storage
        session()->save();

        return redirect()->route('admin.dashboard')->with('success', 'Welcome Admin!');
    }

    public function dashboard(Request $request)
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

        $adminId = Session::get('admin_id');
        
        // Store the timestamp of when admin viewed the dashboard
        if ($adminId) {
            Session::put('leave_notifications_viewed_at_' . $adminId, Carbon::now());
        }
        
        // Get statistics from database
        $totalStaff = \App\Models\Staff::count();
        
        // Get present today (only 'present', 'late', or 'half day' status, excluding 'leave')
        $today = Carbon::today();
        $presentToday = \App\Models\Attendance::whereDate('attendance_date', $today)
            ->whereIn('status', ['present', 'late', 'half day'])
            ->distinct('staff_id')
            ->count('staff_id');
        
        // Get on leave today (approved leave requests for today)
        $onLeaveToday = \App\Models\LeaveRequest::where('status', 'approved')
            ->where(function ($query) use ($today) {
                $query->whereDate('from_date', '<=', $today)
                      ->whereDate('to_date', '>=', $today);
            })
            ->distinct('staff_id')
            ->count('staff_id');
        
        // ✅ No need to check here - middleware already verified admin_id exists
        return view('admin_dashboard', [
            'admin_name' => Session::get('admin_name'),
            'admin_email' => Session::get('admin_email'),
            'totalStaff' => $totalStaff,
            'presentToday' => $presentToday,
            'onLeaveToday' => $onLeaveToday,
        ]);
    }

    public function departments()
    {
        $departments = Department::with('teams.staff')->get();
        return view('admin.departments', compact('departments'));
    }

    public function leaveRequests(Request $request)
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

        $adminId = Session::get('admin_id');
        $status = request('status', 'pending');
        $leaveRequests = LeaveRequest::where('status', $status)
            ->with('staff')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Count pending requests
        $pendingCount = LeaveRequest::where('status', 'pending')->count();
        
        // Store the timestamp of when admin viewed the leave requests page
        if ($adminId) {
            Session::put('leave_notifications_viewed_at_' . $adminId, Carbon::now());
        }
        
        return view('admin.leave_requests', compact('leaveRequests', 'status', 'pendingCount'));
    }

    public function getPendingLeaveCount()
    {
        $adminId = Session::get('admin_id');
        
        // Get the timestamp of when admin last viewed notifications
        $lastViewed = Session::get('leave_notifications_viewed_at_' . $adminId);
        
        // Get the newest pending request creation time
        $newestPending = LeaveRequest::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($newestPending && ($lastViewed === null || $newestPending->created_at > $lastViewed)) {
            $pendingCount = LeaveRequest::where('status', 'pending')->count();
            return response()->json(['count' => $pendingCount]);
        }
        
        return response()->json(['count' => 0]);
    }

    public function approveLease($id)
    {
        $leaveRequest = LeaveRequest::find($id);
        
        if (!$leaveRequest) {
            return back()->withErrors('Leave request not found.');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Leave request approved successfully!');
    }

    public function rejectLeave($id, Request $request)
    {
        $leaveRequest = LeaveRequest::find($id);
        
        if (!$leaveRequest) {
            return back()->withErrors('Leave request not found.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'rejected_at' => Carbon::now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return back()->with('success', 'Leave request rejected.');
    }

    public function logout()
    {
        session()->forget(['admin_id', 'admin_name', 'admin_email']);
        session()->flush();
        
        return redirect('/admin_login')->with('success', 'You have logged out successfully.');
    }
}
