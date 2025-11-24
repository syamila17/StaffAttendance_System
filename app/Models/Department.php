<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';
    protected $primaryKey = 'department_id';
    public $timestamps = true;

    protected $fillable = [
        'department_name',
        'department_code',
        'description',
        'location',
        'manager_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relationships
     */
    
    // One department has many teams
    public function teams()
    {
        return $this->hasMany(Team::class, 'department_id', 'department_id');
    }

    // One department has many staff
    public function staff()
    {
        return $this->hasMany(Staff::class, 'department_id', 'department_id');
    }

    // Department manager (Staff)
    public function manager()
    {
        return $this->belongsTo(Staff::class, 'manager_id', 'staff_id');
    }

    // One department has many reports
    public function reports()
    {
        return $this->hasMany(AttendanceReport::class, 'department_id', 'department_id');
    }
}
