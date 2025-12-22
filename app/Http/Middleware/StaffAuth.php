<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if staff_id exists in session
        if (!session()->has('staff_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Please login as staff first.']);
        }
        
        // Refresh session lifetime on each request
        session()->put('last_activity', Carbon::now()->timestamp);
        
        return $next($request);
    }
}
