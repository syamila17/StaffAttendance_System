<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffProfile extends Model
{
    use HasFactory;

    protected $table = 'staff_profile';
    
    // ✅ SECURITY: Explicitly guard staff_id from mass assignment
    // This prevents any attempt to modify staff_id through fillable or update operations
    protected $guarded = ['staff_id'];
    
    protected $fillable = [
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
