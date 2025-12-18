<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';
    protected $primaryKey = 'team_id';
    public $timestamps = true;

    protected $fillable = [
        'team_name',
        'team_code',
        'department_id',
        'team_lead_id',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relationships
     */
    
    // One team belongs to one department
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    // One team has many staff
    public function staff()
    {
        return $this->hasMany(Staff::class, 'team_id', 'team_id');
    }

    // Team lead (Staff)
    public function teamLead()
    {
        return $this->belongsTo(Staff::class, 'team_lead_id', 'staff_id');
    }

    // One team has many reports
    public function reports()
    {
        return $this->hasMany(AttendanceReport::class, 'team_id', 'team_id');
    }
}
