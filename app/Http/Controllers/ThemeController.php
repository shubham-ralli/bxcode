<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Setting;

class ThemeController extends Controller
{
    public function index(Request $request)
    {
        $themesPath = resource_path('views/themes');
        $allThemes = [];
        $activeTheme = Setting::get('active_theme', 'bxcode-theme');

        if (File::exists($themesPath)) {
            $directories = File::directories($themesPath);

            foreach ($directories as $dir) {
                $folderName = basename($dir);
                $stylePath = $dir . '/style.css';
                $themeData = [
                    'id' => $folderName,
                    'name' => $folderName,
                    'author' => 'Unknown',
                    'version' => '1.0',
                    'description' => '',
                    'active' => ($folderName === $activeTheme)
                ];

                if (File::exists($stylePath)) {
                    $content = File::get($stylePath);
                    if (preg_match('/Theme Name:(.*)$/m', $content, $matches))
                        $themeData['name'] = trim($matches[1]);
                    if (preg_match('/Author:(.*)$/m', $content, $matches))
                        $themeData['author'] = trim($matches[1]);
                    if (preg_match('/Version:(.*)$/m', $content, $matches))
                        $themeData['version'] = trim($matches[1]);
                    if (preg_match('/Description:(.*)$/m', $content, $matches))
                        $themeData['description'] = trim($matches[1]);
                }

                $allThemes[] = $themeData;
            }
        }

        // 1. Calculate Counts
        $counts = [
            'all' => count($allThemes),
            'active' => count(array_filter($allThemes, fn($t) => $t['active'])),
            'inactive' => count(array_filter($allThemes, fn($t) => !$t['active'])),
        ];

        // 2. Filter (Search only for now, Status if needed)
        $search = $request->get('s');
        $status = $request->get('status', 'all');

        $filteredThemes = $allThemes;
        if ($status === 'active') {
            $filteredThemes = array_filter($filteredThemes, fn($t) => $t['active']);
        } elseif ($status === 'inactive') {
            $filteredThemes = array_filter($filteredThemes, fn($t) => !$t['active']);
        }

        if ($search) {
            $filteredThemes = array_filter($filteredThemes, function ($theme) use ($search) {
                return stripos($theme['name'], $search) !== false ||
                    stripos($theme['description'], $search) !== false ||
                    stripos($theme['author'], $search) !== false;
            });
        }

        // 3. Pagination
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $items = array_slice($filteredThemes, $offset, $perPage);
        $themes = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            count($filteredThemes),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.themes.index', compact('themes', 'counts', 'status', 'search'));
    }

    public function activate(Request $request)
    {
        $request->validate([
            'theme_id' => 'required|string'
        ]);

        $themeId = $request->theme_id;
        if (!File::exists(resource_path("views/themes/{$themeId}"))) {
            return back()->with('error', 'Theme not found.');
        }

        Setting::set('active_theme', $themeId);

        return back()->with('success', "Theme '{$themeId}' activated successfully.");
    }

    public function create()
    {
        return view('admin.themes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'theme_zip' => 'required|file|mimes:zip|max:102400', // Max 100MB
        ]);

        try {
            $file = $request->file('theme_zip');
            $originalName = $file->getClientOriginalName();
            \Log::info("Theme Upload Started: {$originalName}");

            $themesPath = resource_path('views/themes');

            // Ensure themes directory exists
            if (!File::exists($themesPath)) {
                try {
                    @File::makeDirectory($themesPath, 0755, true);
                } catch (\Exception $e) {
                    // Continue
                }
            }

            // Try to find a writable temp path (Same strategy as PluginController)
            $tempPath = null;
            $candidates = [
                storage_path('app/temp/theme_' . time()),
                public_path('temp/theme_' . time()),
                sys_get_temp_dir() . '/theme_' . time()
            ];

            foreach ($candidates as $path) {
                try {
                    $parent = dirname($path);
                    if (!File::exists($parent)) {
                        @File::makeDirectory($parent, 0755, true);
                    }
                    if (File::makeDirectory($path, 0755, true)) {
                        $tempPath = $path;
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if (!$tempPath) {
                throw new \Exception("Could not create temporary directory. Please ensure 'storage' or 'public' directories are writable.");
            }

            \Log::info("Extracted to temp: {$tempPath}");

            // Extract ZIP
            $zip = new \ZipArchive;
            if ($zip->open($file->getRealPath()) === true) {
                $zip->extractTo($tempPath);
                $zip->close();
            } else {
                throw new \Exception('Failed to extract ZIP file.');
            }

            // Find the theme folder
            // Behavior: 
            // 1. If zip contains one folder -> Use that folder.
            // 2. If zip contains multiple files/folders -> Create a folder based on zip name.

            $extractedDirs = File::directories($tempPath);
            $extractedFiles = File::files($tempPath);

            $sourcePath = $tempPath;
            $themeFolderName = pathinfo($originalName, PATHINFO_FILENAME);

            // Ignore __MACOSX
            $extractedDirs = array_filter($extractedDirs, function ($d) {
                return basename($d) !== '__MACOSX';
            });

            if (count($extractedDirs) === 1 && count($extractedFiles) === 0) {
                // Perfect structure: Zip contains exactly one folder
                $sourcePath = reset($extractedDirs);
                $themeFolderName = basename($sourcePath);
                \Log::info("Detected single folder structure: {$themeFolderName}");
            } else {
                // Flat structure or multiple folders: Use all content in temp as the theme
                \Log::info("Detected flat structure, using zip name: {$themeFolderName}");
            }

            $destinationPath = $themesPath . '/' . $themeFolderName;

            // Check if theme already exists
            if (File::exists($destinationPath)) {
                File::deleteDirectory($tempPath);
                throw new \Exception("Theme '{$themeFolderName}' already exists. Please delete it first or upload a different theme.");
            }

            // Move to themes directory
            $moved = File::moveDirectory($sourcePath, $destinationPath);

            // Fallback: Copy + Delete
            if (!$moved || !File::exists($destinationPath)) {
                File::copyDirectory($sourcePath, $destinationPath);
                File::deleteDirectory($sourcePath);
            }

            // Clean up root temp if we moved a subdir
            if (File::exists($tempPath)) {
                File::deleteDirectory($tempPath);
            }

            // Final Verification
            if (!File::exists($destinationPath)) {
                throw new \Exception("Failed to move theme files to {$destinationPath}. Check folder permissions.");
            }

            \Log::info("Theme moved successfully to: {$destinationPath}");

            // Optional Activation
            if ($request->has('activate') && $request->activate == 'true') {
                Setting::set('active_theme', $themeFolderName);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Theme uploaded successfully.']);
            }

            return redirect()->route('admin.themes.index')->with('success', 'Theme uploaded successfully.');

        } catch (\Exception $e) {
            \Log::error("Theme Upload Error: " . $e->getMessage());
            // Clean up on error
            if (isset($tempPath) && File::exists($tempPath)) {
                File::deleteDirectory($tempPath);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'theme_id' => 'required|string'
        ]);

        $themeId = $request->theme_id;
        $activeTheme = Setting::get('active_theme', 'bxcode-theme');

        if ($themeId === $activeTheme) {
            return back()->with('error', 'Cannot delete the active theme.');
        }

        $themePath = resource_path("views/themes/{$themeId}");

        if (File::exists($themePath)) {
            File::deleteDirectory($themePath);
            return back()->with('success', 'Theme deleted successfully.');
        }

        return back()->with('error', 'Theme not found.');
    }
}
