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
        // Check ENV for Database setup
        // Check if application key is set or .env file exists
        $appKey = config('app.key');
        $envExists = file_exists(base_path('.env'));

        // Also check if DB is configured (avoid default values)
        $dbName = config('database.connections.mysql.database');
        $isDbConfigured = !empty($dbName) && $dbName !== 'laravel' && $dbName !== 'forge';

        // Consider installed if Key exists AND DB is configured
        $isInstalled = !empty($appKey) && $envExists && $isDbConfigured;

        if (!$isInstalled) {
            // Allow access to install routes and static assets
            if (!$request->is('install') && !$request->is('install/*') && !$request->is('theme/*')) {
                return redirect()->route('install.index');
            }
        } else {
            // If installed, block access to install routes
            if ($request->is('install') || $request->is('install/*')) {
                return redirect('/');
            }
        }

        return $next($request);
    }
}
