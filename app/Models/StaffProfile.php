<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'full_name',
        'email',
        'phone_number',
        'address',
        'position',
        'department',
        'profile_image',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
