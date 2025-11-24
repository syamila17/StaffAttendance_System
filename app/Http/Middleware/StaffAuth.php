<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if staff_id exists in session
        if (!session()->has('staff_id')) {
            return redirect('/login')->withErrors(['error' => 'Please login as staff first.']);
        }
        
        return $next($request);
    }
}
