<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\StaffProfile;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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

        // Check if staff has approved leave for today
        $approvedLeaveToday = LeaveRequest::where('staff_id', $staffId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->first();

        $recentAttendance = Attendance::where('staff_id', $staffId)
            ->orderBy('attendance_date', 'desc')
            ->limit(30)
            ->get();

        return view('attendance', compact('profile', 'todayAttendance', 'recentAttendance', 'approvedLeaveToday'));
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

        // Check if staff has approved leave for today
        $approvedLeaveToday = LeaveRequest::where('staff_id', $staffId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->first();

        if ($approvedLeaveToday) {
            return back()->withErrors(['error' => 'You are on approved leave and cannot check in']);
        }

        $todayAttendance = Attendance::where('staff_id', $staffId)
            ->whereDate('attendance_date', $today)
            ->first();

        // Disable check-in if status is absent
        if ($todayAttendance && $todayAttendance->status === 'absent') {
            return back()->withErrors(['error' => 'Cannot check in when marked as absent']);
        }

        // Capture current time in HH:MM:SS format directly
        $currentTime = date('H:i:s');
        
        // Determine status based on check-in time
        // If check-in is after 12:00 PM (12:00:00), mark as half day
        $checkInHour = (int)date('H');
        $status = ($checkInHour >= 12) ? 'half day' : 'present';
        
        $attendance = Attendance::firstOrCreate(
            [
                'staff_id' => $staffId,
                'attendance_date' => $today
            ],
            [
                'status' => $status,
                'check_in_time' => $currentTime
            ]
        );

        // If check-in doesn't exist yet, update it with current time
        if ($attendance->check_in_time === null) {
            $attendance->update([
                'check_in_time' => $currentTime,
                'status' => $status
            ]);
        }

        return back()->with('success', 'Check-in successful at ' . $currentTime);
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

        // Check if staff has approved leave for today
        $approvedLeaveToday = LeaveRequest::where('staff_id', $staffId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->first();

        if ($approvedLeaveToday) {
            return back()->withErrors(['error' => 'You are on approved leave and cannot check out']);
        }

        $todayAttendance = Attendance::where('staff_id', $staffId)
            ->whereDate('attendance_date', $today)
            ->first();

        // Disable check-out if status is absent
        if ($todayAttendance && $todayAttendance->status === 'absent') {
            return back()->withErrors(['error' => 'Cannot check out when marked as absent']);
        }

        // Capture current time in HH:MM:SS format directly
        $currentTime = date('H:i:s');
        
        $attendance = Attendance::where('staff_id', $staffId)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance) {
            return back()->withErrors(['error' => 'Please check-in first']);
        }

        $attendance->update(['check_out_time' => $currentTime]);

        return back()->with('success', 'Check-out successful at ' . $currentTime);
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

        // Build validation rules based on status
        $validationRules = [
            'status' => 'required|in:present,absent,late,el,on leave,half day',
            'date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:255',
        ];

        // If status is EL, reason is mandatory
        if ($request->input('status') === 'el') {
            $validationRules['el_reason'] = 'required|string|max:1000';
            $validationRules['el_proof_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
        } else {
            $validationRules['el_reason'] = 'nullable|string';
            $validationRules['el_proof_file'] = 'nullable';
        }

        $validated = $request->validate($validationRules);

        // Prepare data for attendance record
        $attendanceData = [
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
        ];

        // Add time fields only for non-leave/absent statuses
        if (!in_array($validated['status'], ['absent', 'on leave', 'el'])) {
            $attendanceData['check_in_time'] = $validated['check_in_time'] ? $validated['check_in_time'] . ':00' : null;
            $attendanceData['check_out_time'] = $validated['check_out_time'] ? $validated['check_out_time'] . ':00' : null;
        } else {
            $attendanceData['check_in_time'] = null;
            $attendanceData['check_out_time'] = null;
        }

        // Handle EL-specific data
        if ($validated['status'] === 'el') {
            $attendanceData['el_reason'] = $validated['el_reason'];
            
            // Handle EL proof file upload
            if ($request->hasFile('el_proof_file')) {
                try {
                    $file = $request->file('el_proof_file');
                    
                    // Validate file
                    $this->validateProofFile($file);

                    // Generate unique filename
                    $staffFolder = 'el_proofs/staff_' . $staffId;
                    $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                    
                    // Store file
                    $path = $file->storeAs($staffFolder, $fileName, 'public');
                    
                    $attendanceData['el_proof_file'] = $file->getClientOriginalName();
                    $attendanceData['el_proof_file_path'] = $path;
                    $attendanceData['el_proof_uploaded_at'] = Carbon::now();
                } catch (\Exception $e) {
                    return back()->withErrors(['el_proof_file' => 'Error uploading file: ' . $e->getMessage()]);
                }
            }
        }

        // Create or update attendance record
        $attendance = Attendance::firstOrCreate(
            [
                'staff_id' => $staffId,
                'attendance_date' => $validated['date']
            ],
            $attendanceData
        );

        // If record exists, update it
        if ($attendance->wasRecentlyCreated === false) {
            $attendance->update($attendanceData);
        }

        return back()->with('success', 'Attendance status updated successfully!');
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
}
