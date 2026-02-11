<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if .env exists at all
        $envPath = base_path('.env');

        // Get database configuration safely
        $dbName = env('DB_DATABASE', '');
        $dbUser = env('DB_USERNAME', '');

        // Check if a users table exists (indicates completed installation)
        $dbConfigured = false;
        try {
            if (!empty($dbName) && $dbName !== 'forge' && $dbName !== 'laravel') {
                // Try to check if database and users table exists
                \DB::connection()->getPdo();
                $dbConfigured = \Schema::hasTable('users');
            }
        } catch (\Exception $e) {
            // Database not configured or not accessible
            $dbConfigured = false;
        }

        if (!$dbConfigured) {
            // Not installed - redirect to installer unless already there
            if (!$request->is('install') && !$request->is('install/*')) {
                return redirect()->route('install.index');
            }
        } else {
            // Already installed - redirect away from installer
            if ($request->is('install') || $request->is('install/*')) {
                return redirect('/');
            }
        }

        return $next($request);
    }
}
