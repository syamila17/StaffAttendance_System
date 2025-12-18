<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceReportDetail extends Model
{
    use HasFactory;

    protected $table = 'attendance_report_details';
    protected $primaryKey = 'detail_id';
    public $timestamps = true;

    protected $fillable = [
        'report_id',
        'staff_id',
        'attendance_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'status',
        'duration_minutes',
        'remarks',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'status' => 'string',
    ];

    /**
     * Relationships
     */
    
    // One detail belongs to one report
    public function report()
    {
        return $this->belongsTo(AttendanceReport::class, 'report_id', 'report_id');
    }

    // One detail belongs to one staff
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    // One detail belongs to optional attendance record
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id', 'id');
    }
}
