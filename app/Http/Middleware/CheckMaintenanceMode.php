<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled in the database settings
        if (setting('maintenance_mode', 'false') === 'true') {
            
            // Allow Admins to bypass maintenance mode so they can fix things or turn it off
            if (auth()->check() && auth()->user()->hasRole('Admin')) {
                return $next($request);
            }

            // Exclude login routes so users can still attempt to login (and admins can login)
            if ($request->is('/') || $request->is('login') || $request->is('logout')) {
                return $next($request);
            }

            // If it's an API request or expects JSON, return a 503 JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The system is currently undergoing maintenance. Please try again later.'
                ], 503);
            }

            // Otherwise show the maintenance view
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
