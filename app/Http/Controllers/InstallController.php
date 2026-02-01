<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstallController extends Controller
{
    public function index()
    {
        // Create .env from .env.example if it doesn't exist
        if (!file_exists(base_path('.env'))) {
            if (file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), base_path('.env'));
                chmod(base_path('.env'), 0666);

                // Generate APP_KEY immediately
                \Artisan::call('key:generate');

                // Reload configuration
                \Artisan::call('config:clear');
            }
        }

        return view('install.index');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'db_name' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:8',
        ]);

        // Create .env from .env.example if it doesn't exist
        if (!file_exists(base_path('.env'))) {
            if (file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), base_path('.env'));
                chmod(base_path('.env'), 0666);
            } else {
                return back()->with('error', '.env.example file not found');
            }
        }

        // Generate APP_KEY if not set
        if (empty(config('app.key'))) {
            \Artisan::call('key:generate');
        }

        // Update .env
        $this->updateEnv([
            'APP_URL' => $request->root(),
            'DB_DATABASE' => $request->db_name,
            'DB_USERNAME' => $request->db_username,
            'DB_PASSWORD' => $request->db_password ?? '',
        ]);

        // Reconnect DB
        config([
            'database.connections.mysql.database' => $request->db_name,
            'database.connections.mysql.username' => $request->db_username,
            'database.connections.mysql.password' => $request->db_password ?? '',
        ]);
        \DB::purge('mysql');
        \DB::reconnect('mysql');

        // Migrate
        \Artisan::call('migrate', ['--force' => true]);

        // Create Admin User
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => $request->admin_email,
            'password' => \Hash::make($request->admin_password),
        ]);


        return redirect()->route('login');
    }

    protected function updateEnv($data)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $env = file_get_contents($path);
            foreach ($data as $key => $value) {
                $value = '"' . trim($value) . '"'; // Quote the value
                if (strpos($env, $key . '=') !== false) {
                    $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
                } else {
                    $env .= "\n{$key}={$value}";
                }
            }
            file_put_contents($path, $env);
        }
    }
}
