<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    public $timestamps = false;

    protected $fillable = [
        'staff_name',
        'staff_email',
        'staff_password',
        'team_id',
        'created_at'
    ];

    public function profile()
    {
        return $this->hasOne(StaffProfile::class, 'staff_id', 'staff_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'staff_id', 'staff_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    public function teamsManaged()
    {
        return $this->hasMany(Team::class, 'team_lead_id', 'staff_id');
    }

    public function departmentsManaged()
    {
        return $this->hasMany(Department::class, 'manager_id', 'staff_id');
    }

    public function reports()
    {
        return $this->hasMany(AttendanceReport::class, 'staff_id', 'staff_id');
    }
}
