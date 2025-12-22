<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'leave_requests';
    protected $primaryKey = 'leave_request_id';
    protected $fillable = [
        'staff_id',
        'leave_type',
        'from_date',
        'to_date',
        'reason',
        'status',
        'admin_notes',
        'approved_at',
        'rejected_at',
        'proof_file',
        'proof_file_path',
        'proof_uploaded_at'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'proof_uploaded_at' => 'datetime',
    ];

    /**
     * Get the staff member associated with this leave request
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    /**
     * Scope to get pending leave requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved leave requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected leave requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if proof file is required for this leave type
     */
    public function isProofRequired(): bool
    {
        return in_array($this->leave_type, ['Sick Leave']);
    }

    /**
     * Check if proof file is optional for this leave type
     */
    public function isProofOptional(): bool
    {
        return in_array($this->leave_type, ['Emergency Leave']);
    }

    /**
     * Get the full URL to the proof file
     */
    public function getProofFileUrl(): ?string
    {
        if ($this->proof_file_path) {
            return asset('storage/' . $this->proof_file_path);
        }
        return null;
    }

    /**
     * Check if proof file exists
     */
    public function hasProofFile(): bool
    {
        return !is_null($this->proof_file) && !is_null($this->proof_file_path);
    }
}
