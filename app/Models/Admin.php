<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'admin_id';
    public $timestamps = false;

    protected $fillable = [
        'admin_name',
        'admin_email',
        'admin_password',
        'leave_notifications_viewed',
        'leave_notifications_viewed_at',
    ];

    protected $casts = [
        'leave_notifications_viewed' => 'boolean',
        'leave_notifications_viewed_at' => 'datetime',
    ];
}
