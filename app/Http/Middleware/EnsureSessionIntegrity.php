<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionIntegrity
{
    /**
     * Handle an incoming request.
     *
     * Ensures session directory exists and has proper permissions
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionPath = storage_path('framework/sessions');
        
        // Create sessions directory if it doesn't exist
        if (!is_dir($sessionPath)) {
            @mkdir($sessionPath, 0755, true);
        }
        
        // Ensure the session is started
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }
        
        return $next($request);
    }
}
