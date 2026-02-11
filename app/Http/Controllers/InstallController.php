<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstallController extends Controller
{
    public function index()
    {
        return view('install.index');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        set_time_limit(300);

        $request->validate([
            'db_name' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:8',
        ]);

        try {
            // 0. Auto-Create Database if it doesn't exist
            try {
                $pdo = new \PDO("mysql:host=127.0.0.1;port=3306;", $request->db_username, $request->db_password);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $dbName = str_replace('`', '``', $request->db_name);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Throwable $e) {
                // Ignore creation errors here; standard connection test below will report auth/connection issues
            }

            // 1. Test Database Connection FIRST
            // We use a temporary connection configuration to test before writing to .env
            $tempConfig = [
                'driver' => 'mysql',
                'host' => '127.0.0.1', // Assuming localhost, or get from request if fields added
                'port' => '3306',
                'database' => $request->db_name,
                'username' => $request->db_username,
                'password' => $request->db_password ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ];

            // Set the temp config to a new connection name 'install_test'
            config(['database.connections.install_test' => $tempConfig]);

            // Try to connect
            \DB::connection('install_test')->getPdo();

            // If we reached here, connection is successful!

            // 2. Ensure .env exists
            // 2. Ensure .env exists
            if (!file_exists(base_path('.env')) && file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), base_path('.env'));

                // Use temporary key if available to preserve session, otherwise generate new
                $tempKeyFile = storage_path('app/installation_key');
                if (file_exists($tempKeyFile)) {
                    $key = trim(file_get_contents($tempKeyFile));
                } else {
                    $key = 'base64:' . base64_encode(random_bytes(32));
                }

                $this->updateEnv(['APP_KEY' => $key]);
            }

            // 3. Update .env with database credentials - MOVED TO END TO PREVENT RESTART-LOOP
            // We configure runtime connection below instead.

            // 4. Configure default database connection for migrations
            config([
                'database.connections.mysql.host' => '127.0.0.1',
                'database.connections.mysql.database' => $request->db_name,
                'database.connections.mysql.username' => $request->db_username,
                'database.connections.mysql.password' => $request->db_password ?? '',
            ]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');

            // 5. Run migrations (Fresh to ensure clean slate and avoid 'table exists' errors)
            \Artisan::call('migrate:fresh', [
                '--force' => true,
                '--seed' => false // We seed manually below
            ]);

            // 6. Create Admin User
            $user = \App\Models\User::create([
                'name' => 'Admin',
                'email' => $request->admin_email,
                'password' => $request->admin_password,
                'role' => 'administrator',
            ]);

            // 7. Seed Default Settings
            if (class_exists('App\Models\Setting') && \Schema::hasTable('settings')) {
                \App\Models\Setting::firstOrCreate(['key' => 'site_title'], ['value' => 'BxCode CMS']);
                \App\Models\Setting::firstOrCreate(['key' => 'tagline'], ['value' => 'Just another BxCode CMS site']);
                \App\Models\Setting::firstOrCreate(['key' => 'active_theme'], ['value' => 'bxcode-theme']);
                \App\Models\Setting::firstOrCreate(['key' => 'posts_per_page'], ['value' => '10']);
                \App\Models\Setting::firstOrCreate(['key' => 'admin_email'], ['value' => $request->admin_email]);
            }

            // 8. FINAL STEP: Persist Configuration to .env
            // We do this LAST so if server restarts (local dev), the heavy lifting (migrations/user) is already done.
            $this->updateEnv([
                'APP_URL' => $request->root(),
                'DB_HOST' => '127.0.0.1',
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password ?? '',
            ]);

            \Auth::login($user);
            return redirect('/')->with('success', 'Installation completed successfully!');

        } catch (\PDOException $e) {
            $errorCode = $e->getCode();
            $errorMessage = 'Database Connection Error: ';

            if ($errorCode == 1045) {
                $errorMessage .= 'Invalid Database Username or Password. Please check your credentials.';
            } elseif ($errorCode == 1049) {
                $errorMessage .= 'Database "' . $request->db_name . '" does not exist. Please create it first.';
            } else {
                // Try to parse the SQL state error for more info if code is generic
                if (str_contains($e->getMessage(), 'Access denied')) {
                    $errorMessage .= 'Invalid Database Username or Password.';
                } elseif (str_contains($e->getMessage(), 'Unknown database')) {
                    $errorMessage .= 'Database "' . $request->db_name . '" does not exist.';
                } else {
                    $errorMessage .= $e->getMessage();
                }
            }

            return back()->withErrors(['db_error' => $errorMessage])->withInput();
        } catch (\Throwable $e) {
            \Log::error('Installation Failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            // If installation fails for other reasons
            return back()->withErrors([
                'error' => 'Installation failed: ' . $e->getMessage()
            ])->withInput();
        }
    }

    protected function updateEnv($data)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $env = file_get_contents($path);
            $originalEnv = $env;

            foreach ($data as $key => $value) {
                // Don't quote APP_KEY if it starts with base64:
                if ($key === 'APP_KEY' || strpos($value, 'base64:') === 0) {
                    $formattedValue = $value;
                } else {
                    $formattedValue = '"' . trim($value) . '"';
                }

                // Check if key exists
                if (preg_match("/^{$key}=.*/m", $env, $matches)) {
                    // Key exists, check if value is different
                    // Escape special characters for regex match check if needed, or just compare
                    // We simply replace and see if string changes at end
                    $env = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $env);
                } else {
                    // Key doesn't exist, append
                    $env .= "\n{$key}={$formattedValue}";
                }
            }

            // Only write if content changed
            if ($env !== $originalEnv) {
                file_put_contents($path, $env);
                // If we wrote to .env, giving a small delay might help, but server kill is usually instant.
                // However, since we are retrying, this block WON'T run, solving the user's issue.
            }
        }
    }
}
