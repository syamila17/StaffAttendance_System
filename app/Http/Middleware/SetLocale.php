<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if language is passed as query parameter
        if ($request->has('lang')) {
            $lang = $request->query('lang');
            if (in_array($lang, ['en', 'ms'])) {
                app()->setLocale($lang);
                session(['locale' => $lang]);
            }
        } else if (session()->has('locale')) {
            // Use previously set language from session
            app()->setLocale(session('locale'));
        } else {
            // Default to English
            app()->setLocale('en');
            session(['locale' => 'en']);
        }

        return $next($request);
    }
}
