<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    /**
     * Database Configuration
     * - Table: staff (in staffAttend_data database)
     * - Primary Key: staff_id (VARCHAR(50), not auto-incrementing)
     * - Format: ST001, ST002, etc.
     */
    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    public $incrementing = false;  // staff_id is not auto-incrementing
    protected $keyType = 'string';  // staff_id is a string, not integer
    public $timestamps = false;    // Disable timestamps if not used

    protected $fillable = [
        'staff_id',
        'staff_name',
        'staff_email',
        'staff_password',
        'department_id',
        'team_id',
        'annual_leave_balance',
    ];

    // Hidden attributes (don't expose password in API responses)
    protected $hidden = [
        'staff_password'
    ];

    /**
     * Type Casting
     * Ensures staff_id is always treated as string, never integer
     */
    protected $casts = [
        'staff_id' => 'string',
        'annual_leave_balance' => 'decimal:2',
    ];

    /**
     * Boot the model
     * Auto-generate staff_id before creating new staff
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-generate staff_id if not provided
            if (empty($model->staff_id)) {
                $model->staff_id = self::generateStaffId();
            }
        });
    }

    /**
     * Generate next staff_id in sequence
     * Format: st followed by 3+ digits (e.g., st001, st002, st003)
     * This creates lowercase formatted staff IDs
     */
    public static function generateStaffId()
    {
        // Get the highest counter currently in use by ordering by staff_id
        $lastStaff = self::orderBy('staff_id', 'desc')->first();
        
        if (!$lastStaff || !$lastStaff->staff_id) {
            // If no staff or no staff_id, start with st001
            return 'st001';
        }

        // Extract the numeric portion from the last staff_id (e.g., "123" from "st123")
        preg_match('/(\d+)$/', $lastStaff->staff_id, $matches);
        
        if (!empty($matches[1])) {
            $lastCounter = intval($matches[1]);
            $nextCounter = $lastCounter + 1;
            // Extract the prefix (e.g., "st" from "st001")
            $prefix = preg_replace('/\d+$/', '', $lastStaff->staff_id);
            // Pad the new counter with zeros to match the length of the last one
            $paddingLength = strlen($matches[1]);
            return $prefix . str_pad($nextCounter, $paddingLength, '0', STR_PAD_LEFT);
        }

        // Fallback if pattern doesn't match
        return 'st001';
    }

    /**
     * RELATIONSHIPS
     */

    /**
     * Get the staff profile associated with this staff member
     */
    public function profile()
    {
        return $this->hasOne(StaffProfile::class, 'staff_id', 'staff_id');
    }

    /**
     * Get all attendance records for this staff member
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'staff_id', 'staff_id');
    }

    /**
     * Get all leave requests for this staff member
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'staff_id', 'staff_id');
    }

    /**
     * Get the department this staff member belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    /**
     * Get the team this staff member belongs to
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    /**
     * Get teams where this staff member is the team lead
     */
    public function teamsManaged()
    {
        return $this->hasMany(Team::class, 'team_lead_id', 'staff_id');
    }

    /**
     * Get departments where this staff member is the manager
     */
    public function departmentsManaged()
    {
        return $this->hasMany(Department::class, 'manager_id', 'staff_id');
    }

    /**
     * Get all attendance reports for this staff member
     */
    public function reports()
    {
        return $this->hasMany(AttendanceReport::class, 'staff_id', 'staff_id');
    }

    /**
     * UTILITY METHODS
     */

    /**
     * Get all pending leave requests for this staff member
     */
    public function pendingLeaveRequests()
    {
        return $this->leaveRequests()->where('status', 'pending');
    }

    /**
     * Get all approved leave requests for this staff member
     */
    public function approvedLeaveRequests()
    {
        return $this->leaveRequests()->where('status', 'approved');
    }

    /**
     * Get today's attendance record if it exists
     */
    public function todayAttendance()
    {
        return $this->attendance()->whereDate('attendance_date', now()->toDateString())->first();
    }

    /**
     * Check if staff member is checked in today
     */
    public function isCheckedInToday()
    {
        $today = $this->todayAttendance();
        return $today && $today->check_in_time !== null;
    }

    /**
     * Check if staff member is checked out today
     */
    public function isCheckedOutToday()
    {
        $today = $this->todayAttendance();
        return $today && $today->check_out_time !== null;
    }
}

