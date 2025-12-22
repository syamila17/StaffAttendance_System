<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if admin_id exists in session
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->withErrors(['error' => 'Please login as admin first.']);
        }
        
        // Refresh session lifetime on each request
        session()->put('last_activity', Carbon::now()->timestamp);
        
        return $next($request);
    }
}
