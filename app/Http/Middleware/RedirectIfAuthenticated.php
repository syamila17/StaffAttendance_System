<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // If user is already logged in as staff, redirect to staff dashboard
        if (Session::has('staff_id')) {
            return redirect()->route('staff.dashboard');
        }

        // If user is already logged in as admin, redirect to admin dashboard
        if (Session::has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        // User is not authenticated, allow request to proceed
        return $next($request);
    }
}
