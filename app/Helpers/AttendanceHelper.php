<?php

namespace App\Helpers;

use Carbon\Carbon;

class AttendanceHelper
{
    /**
     * Format duration in hours and minutes format: "?h ?m"
     */
    public static function formatDuration($checkInTime, $checkOutTime)
    {
        if (!$checkInTime || !$checkOutTime) {
            return '--h --m';
        }

        try {
            $checkIn = Carbon::createFromFormat('H:i:s', $checkInTime);
            $checkOut = Carbon::createFromFormat('H:i:s', $checkOutTime);
            
            $totalMinutes = abs($checkOut->diffInMinutes($checkIn));
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            
            return "{$hours}h {$minutes}m";
        } catch (\Exception $e) {
            return '--h --m';
        }
    }

    /**
     * Get status color class
     */
    public static function getStatusColorClass($status)
    {
        return match($status) {
            'present' => 'text-green-400',
            'absent' => 'text-red-400',
            'late' => 'text-yellow-400',
            'el' => 'text-orange-400',
            'on leave' => 'text-blue-400',
            'leave' => 'text-blue-400',
            'half day' => 'text-purple-400',
            default => 'text-gray-400',
        };
    }

    /**
     * Get status background class
     */
    public static function getStatusBgClass($status)
    {
        return match($status) {
            'present' => 'bg-green-500/20 text-green-300',
            'absent' => 'bg-red-500/20 text-red-300',
            'late' => 'bg-yellow-500/20 text-yellow-300',
            'el' => 'bg-orange-500/20 text-orange-300',
            'on leave' => 'bg-blue-500/20 text-blue-300',
            'leave' => 'bg-blue-500/20 text-blue-300',
            'half day' => 'bg-purple-500/20 text-purple-300',
            default => 'bg-gray-500/20 text-gray-300',
        };
    }
}
