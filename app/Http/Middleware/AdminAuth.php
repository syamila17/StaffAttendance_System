<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if admin_id exists in session
        if (!session()->has('admin_id')) {
            return redirect('/admin_login')->withErrors(['error' => 'Please login as admin first.']);
        }
        
        return $next($request);
    }
}
