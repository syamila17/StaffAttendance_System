<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffSession extends Model
{
    use HasFactory;

    protected $table = 'staff_sessions';
    protected $fillable = [
        'staff_id',
        'session_id',
        'ip_address',
        'user_agent',
        'logged_in_at',
        'last_activity_at',
    ];

    protected $casts = [
        'logged_in_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the staff member associated with this session
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }
}
