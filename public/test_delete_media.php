<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "<h1>Media Deletion Test</h1>";

try {
    // 1. Create a dummy file
    $filename = 'test-delete-' . time() . '.txt';
    $directory = date('Y/m');
    $content = 'This is a test file for deletion.';

    // Store using 'uploads' disk
    Storage::disk('uploads')->put("{$directory}/{$filename}", $content);

    $fullPath = "/uploads/{$directory}/{$filename}";
    echo "Created file at: $fullPath <br>";

    // 2. Create DB record
    $media = Media::create([
        'filename' => $filename,
        'path' => $fullPath,
        'mime_type' => 'text/plain',
        'size' => 100,
        'title' => 'Deletion Test',
    ]);
    echo "Created Media ID: {$media->id} <br>";

    // 3. Try to delete using Controller Logic
    echo "Attempting deletion logic... <br>";

    $relativePath = str_replace(['/uploads/', 'uploads/'], '', $media->path);
    echo "Computed Relative Path: '$relativePath' <br>";

    if (Storage::disk('uploads')->exists($relativePath)) {
        echo "File exists on disk. Deleting... <br>";
        $deleted = Storage::disk('uploads')->delete($relativePath);
        echo "Storage delete result: " . ($deleted ? 'TRUE' : 'FALSE') . "<br>";
    } else {
        echo "File NOT found on disk using relative path.<br>";
    }

    $media->delete();
    echo "DB Record deleted.<br>";

    // 4. Verify
    if (Storage::disk('uploads')->exists($relativePath)) {
        echo "<strong style='color:red'>FAILURE: File still exists on disk!</strong>";
    } else {
        echo "<strong style='color:green'>SUCCESS: File removed from disk.</strong>";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
