<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceReport extends Model
{
    use HasFactory;

    protected $table = 'attendance_reports';
    protected $primaryKey = 'report_id';
    public $timestamps = true;

    protected $fillable = [
        'admin_id',
        'report_name',
        'start_date',
        'end_date',
        'report_type',
        'department_id',
        'team_id',
        'staff_id',
        'total_days',
        'present_days',
        'absent_days',
        'late_days',
        'leave_days',
        'attendance_percentage',
        'remarks',
        'generated_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'generated_at' => 'datetime',
        'attendance_percentage' => 'float',
    ];

    /**
     * Relationships
     */
    
    // One report belongs to one admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    // One report belongs to optional department
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    // One report belongs to optional team
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    // One report belongs to optional staff
    public function staffMember()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    // One report has many details
    public function details()
    {
        return $this->hasMany(AttendanceReportDetail::class, 'report_id', 'report_id');
    }

    /**
     * Scopes for query building
     */
    
    public function scopeByReportType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate])
                     ->orWhereBetween('end_date', [$startDate, $endDate]);
    }
}
