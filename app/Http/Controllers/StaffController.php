<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Attendance;
use App\Models\StaffProfile;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;


class StaffController extends Controller
{
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

        // Check if today is a leave day
        $todayLeave = LeaveRequest::where('staff_id', $staffId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->first();

        // If today is a leave and no attendance record, create one or indicate leave status
        if ($todayLeave && !$todayAttendance) {
            $todayAttendance = (object)[
                'status' => 'on leave',
                'check_in_time' => null,
                'check_out_time' => null,
                'remarks' => 'Approved Leave: ' . $todayLeave->leave_type,
                'is_leave' => true
            ];
        } elseif ($todayLeave && $todayAttendance) {
            // If both exist, mark it as leave
            $todayAttendance->is_leave = true;
            $todayAttendance->remarks = 'Approved Leave: ' . $todayLeave->leave_type;
        }

        // Get selected month or default to current month
        $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $currentMonth = $selectedMonth;
        $currentMonthFormatted = Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y');
        
        // Get attendance statistics for selected month
        $attendanceStats = $this->getMonthlyAttendanceStats($staffId, $selectedMonth);

        // Extract counts from stats
        $totalPresent = $attendanceStats['present'];
        $totalAbsent = $attendanceStats['absent'];
        $totalLate = $attendanceStats['late'];
        $totalEL = $attendanceStats['el'];
        $totalOnLeave = $attendanceStats['on_leave'];
        $totalHalfDay = $attendanceStats['half_day'];

        // Get recent attendance history (last 30 records)
        $recentAttendance = Attendance::where('staff_id', $staffId)
            ->orderBy('attendance_date', 'desc')
            ->limit(30)
            ->get();

        // Grafana pie chart URL
        $grafanaPieChartUrl = 'http://localhost:3000/d-solo/adtx5zp/attendance-dashboard?orgId=1&panelId=1&__feature.dashboardSceneSolo=true';

        $availableMonths = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $availableMonths[$month->format('Y-m')] = $month->format('F Y');
        }

    return view('staff_dashboard', compact(
        'staffName', 
        'staffEmail',
        'profile',
        'todayAttendance',
        'totalPresent',
        'totalAbsent',
        'totalLate',
        'totalEL',
        'totalOnLeave',
        'totalHalfDay',
        'currentMonth',
        'selectedMonth',
        'currentMonthFormatted',
        'attendanceStats',
        'recentAttendance',
        'grafanaPieChartUrl',
        'staffId',
        'availableMonths',
    ));
}

public function getPieChartData(Request $request)
        {
            try {
                $staffId = Session::get('staff_id');
                $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));
        
                // Use the same stats function as dashboard
                $stats = $this->getMonthlyAttendanceStats($staffId, $selectedMonth);
                
        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['Present', 'Absent', 'Late', 'Leave', 'Half Day'],
                'datasets' => [
                    [
                        'label' => 'Attendance Status',
                        'data' => [
                            (int)$stats['present'],
                            (int)$stats['absent'],
                            (int)$stats['late'],
                            (int)$stats['on_leave'],
                            (int)$stats['half_day']
                        ],
                        'backgroundColor' => [
                            'rgba(34, 197, 94, 0.7)',    // Green - Present
                            'rgba(239, 68, 68, 0.7)',    // Red - Absent
                            'rgba(234, 179, 8, 0.7)',    // Yellow - Late
                            'rgba(59, 130, 246, 0.7)',   // Blue - Leave
                            'rgba(168, 85, 247, 0.7)'    // Purple - Half Day
                        ],
                        'borderColor' => [
                            'rgba(34, 197, 94, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(234, 179, 8, 1)',
                            'rgba(59, 130, 246, 1)',
                            'rgba(168, 85, 247, 1)'
                        ],
                        'borderWidth' => 2
                    ]
                ]
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading chart data: ' . $e->getMessage()
        ], 500);
        }
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
            'reason' => $request->leave_type === 'Other' ? 'required|string|max:1000' : 'nullable|string|max:1000',
            'proof_file' => $this->getProofFileValidationRules($request->leave_type),
        ]);

        // Handle file upload
        $proofFileName = null;
        $proofFilePath = null;

        if ($request->hasFile('proof_file')) {
            try {
                $file = $request->file('proof_file');
                
                // Validate file
                $this->validateProofFile($file);

                // Generate unique filename
                $staffFolder = 'leave_proofs/staff_' . $staffId;
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                
                // Store file
                $path = $file->storeAs($staffFolder, $fileName, 'public');
                
                $proofFileName = $file->getClientOriginalName();
                $proofFilePath = $path;
            } catch (\Exception $e) {
                return redirect()->route('staff.apply-leave')
                    ->withErrors(['proof_file' => 'Error uploading file: ' . $e->getMessage()]);
            }
        }

        // Create the leave request
        LeaveRequest::create([
            'staff_id' => $staffId,
            'leave_type' => $validated['leave_type'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
            'proof_file' => $proofFileName,
            'proof_file_path' => $proofFilePath,
            'proof_uploaded_at' => $proofFileName ? Carbon::now() : null,
        ]);

        return redirect()->route('staff.apply-leave')->with('success', 'Leave request submitted successfully! The admin will review it soon.');
    }

    /**
     * Get validation rules for proof file based on leave type
     */
    private function getProofFileValidationRules($leaveType): string|array
    {
        if ($leaveType === 'Sick Leave') {
            // Proof is mandatory for sick leave
            return 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
        } elseif ($leaveType === 'Emergency Leave') {
            // Proof is optional for emergency leave
            return 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
        }
        // No proof required for other leave types
        return 'nullable';
    }

    /**
     * Validate proof file
     */
    private function validateProofFile($file): void
    {
        $allowedMimes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedMimes)) {
            throw new \Exception('Invalid file type. Allowed types: PDF, JPG, PNG, DOC, DOCX');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('File size exceeds 5MB limit');
        }
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

        // Get all leave requests using string staff_id (data is actually STRING despite column type)
        $today = Carbon::today();
        $allLeaves = LeaveRequest::where('staff_id', $staffId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Count by status (count all leaves regardless of date) - use whereStrict for exact matching
        $pendingCount = $allLeaves->whereStrict('status', 'pending')->count();
        $approvedCount = $allLeaves->whereStrict('status', 'approved')->count();
        $rejectedCount = $allLeaves->whereStrict('status', 'rejected')->count();
        $totalRequests = $allLeaves->count();

        // Filter based on request parameter (show all leaves, not just future ones)
        $filter = request('filter', 'all');
        if ($filter === 'all') {
            $filteredLeaves = $allLeaves;
        } else {
            $filteredLeaves = $allLeaves->whereStrict('status', $filter);
        }

        // Calculate off days for current month
        $currentMonth = date('F Y');
        $currentYear = date('Y');
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $offDaysThisMonth = 0;
        foreach ($allLeaves->whereStrict('status', 'approved') as $leave) {
            // Check if leave overlaps with current month
            $fromDate = Carbon::parse($leave->from_date);
            $toDate = Carbon::parse($leave->to_date);
            
            if ($toDate >= $currentMonthStart && $fromDate <= $currentMonthEnd) {
                // Calculate overlap between leave dates and current month
                $start = $fromDate->max($currentMonthStart);
                $end = $toDate->min($currentMonthEnd);
                $daysInMonth = $start->diffInDays($end) + 1;
                $offDaysThisMonth += $daysInMonth;
            }
        }
        $offDaysThisMonth = (int)$offDaysThisMonth;

        // Calculate annual leave statistics
        $totalAnnualLeave = 20; // Default annual leave
        $usedLeave = 0;

        // Calculate used annual leave (count only the days in current year)
        $allApprovedLeaves = $allLeaves->whereStrict('status', 'approved');
        
        foreach ($allApprovedLeaves as $leave) {
            // Only count Annual Leave type
            if ($leave->leave_type !== 'Annual Leave') {
                continue;
            }
            
            $fromDate = Carbon::parse($leave->from_date);
            $toDate = Carbon::parse($leave->to_date);
            $fromYear = $fromDate->format('Y');
            $toYear = $toDate->format('Y');
            
            // Count only days that fall in the current year
            if ($fromYear == $currentYear || $toYear == $currentYear) {
                // If leave spans multiple years, only count the days in current year
                $yearStart = Carbon::createFromFormat('Y-m-d', $currentYear . '-01-01');
                $yearEnd = Carbon::createFromFormat('Y-m-d', $currentYear . '-12-31');
                
                // Calculate the overlap with current year
                $countStart = $fromDate->max($yearStart);
                $countEnd = $toDate->min($yearEnd);
                
                if ($countStart <= $countEnd) {
                    $daysUsed = $countStart->diffInDays($countEnd) + 1;
                    $usedLeave += $daysUsed;
                }
            }
        }
        
        $usedLeave = (int)$usedLeave;
        $remainingBalance = (int)($totalAnnualLeave - $usedLeave);

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

    /**
     * Download proof file for a leave request
     */
    public function downloadProofFile($id)
    {
        $leaveRequest = LeaveRequest::find($id);

        if (!$leaveRequest) {
            return abort(404, 'Leave request not found');
        }

        // Check authorization
        $staffId = Session::get('staff_id');
        $adminId = Session::get('admin_id');

        // Only staff can download their own proof, or admins can download any
        if (!$adminId && $leaveRequest->staff_id !== $staffId) {
            return abort(403, 'Unauthorized to download this file');
        }

        if (!$leaveRequest->hasProofFile()) {
            return abort(404, 'Proof file not found for this leave request');
        }

        if (!Storage::disk('public')->exists($leaveRequest->proof_file_path)) {
            return abort(404, 'Proof file not found on server');
        }

        return Storage::disk('public')->download($leaveRequest->proof_file_path, $leaveRequest->proof_file);
    }

    /**
     * Get monthly attendance statistics
     * @param int $staffId
     * @param string $month Format: 'Y-m' (e.g., '2025-12')
     * @return array
     */
    private function getMonthlyAttendanceStats($staffId, $month)
    {
        // Parse month string
        $monthParts = explode('-', $month);
        $year = (int)$monthParts[0];
        $monthNumber = (int)$monthParts[1];

        // Create start and end dates for the month
        $startDate = Carbon::createFromDate($year, $monthNumber, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $monthNumber, 1)->endOfMonth();

        // Get all attendance records for this month
        $attendanceRecords = Attendance::where('staff_id', $staffId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        // Count each status from attendance records
        $stats = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'el' => 0,
            'on_leave' => 0,
            'half_day' => 0,
            'total_days' => 0,
        ];

        foreach ($attendanceRecords as $record) {
            $status = strtolower($record->status);
            
            if ($status === 'present') {
                $stats['present']++;
            } elseif ($status === 'absent') {
                $stats['absent']++;
            } elseif ($status === 'late') {
                $stats['late']++;
            } elseif ($status === 'el' || $status === 'emergency leave') {
                $stats['el']++;
            } elseif ($status === 'half day') {
                $stats['half_day']++;
            }
            
            $stats['total_days']++;
        }

        // Count on_leave days from leave_requests table (approved leaves only)
        $approvedLeaves = LeaveRequest::where('staff_id', $staffId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $endDate)
            ->where('to_date', '>=', $startDate)
            ->get();

        $onLeaveDays = 0;
        foreach ($approvedLeaves as $leave) {
            // Calculate days in the selected month for this leave request
            $leaveStart = $leave->from_date->greaterThan($startDate) ? $leave->from_date : $startDate;
            $leaveEnd = $leave->to_date->lessThan($endDate) ? $leave->to_date : $endDate;
            $daysInMonth = (int)($leaveStart->diffInDays($leaveEnd) + 1);
            $onLeaveDays += $daysInMonth;
        }
        $stats['on_leave'] = (int)$onLeaveDays;

        // Calculate grand total (all statuses combined)
        $grandTotal = $stats['present'] + $stats['absent'] + $stats['late'] + $stats['el'] + $stats['on_leave'] + $stats['half_day'];

        // Calculate percentages based on grand total
        if ($grandTotal > 0) {
            $stats['present_percentage'] = (int)round(($stats['present'] / $grandTotal) * 100);
            $stats['absent_percentage'] = (int)round(($stats['absent'] / $grandTotal) * 100);
            $stats['late_percentage'] = (int)round(($stats['late'] / $grandTotal) * 100);
            $stats['el_percentage'] = (int)round(($stats['el'] / $grandTotal) * 100);
            $stats['on_leave_percentage'] = (int)round(($stats['on_leave'] / $grandTotal) * 100);
            $stats['half_day_percentage'] = (int)round(($stats['half_day'] / $grandTotal) * 100);
        } else {
            $stats['present_percentage'] = 0;
            $stats['absent_percentage'] = 0;
            $stats['late_percentage'] = 0;
            $stats['el_percentage'] = 0;
            $stats['on_leave_percentage'] = 0;
            $stats['half_day_percentage'] = 0;
        }

        return $stats;
    }

    /**
     * Prepare data for pie chart display
     * @param array $stats
     * @return array
     */
    private function preparePieChartData($stats)
    {
        return [
            'labels' => ['Present', 'Absent', 'Late', 'Leave', 'Half Day'],
            'data' => [
                $stats['present'],
                $stats['absent'],
                $stats['late'],
                $stats['on_leave'],
                $stats['half_day'],
            ],
            'percentages' => [
                $stats['present_percentage'],
                $stats['absent_percentage'],
                $stats['late_percentage'],
                $stats['on_leave_percentage'],
                $stats['half_day_percentage'],
            ],
            'colors' => ['#22c55e', '#ef4444', '#eab308', '#3b82f6', '#a855f7'],
        ];
    }
}
