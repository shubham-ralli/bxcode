<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "<h1>Create Persistent Media</h1>";

try {
    // 1. Create a dummy file
    $filename = 'ui-test-' . time() . '.txt';
    $directory = date('Y/m');
    $content = 'This file is for testing UI deletion.';

    // Store using 'uploads' disk
    Storage::disk('uploads')->put("{$directory}/{$filename}", $content);

    $fullPath = "/uploads/{$directory}/{$filename}";

    // 2. Create DB record
    $media = Media::create([
        'filename' => $filename,
        'path' => $fullPath,
        'mime_type' => 'text/plain',
        'size' => 100,
        'title' => 'UI Deletion Test',
        'alt_text' => 'Delete me via UI',
    ]);

    echo "Created Media ID: {$media->id} <br>";
    echo "Filename: $filename <br>";
    echo "Ready for UI deletion test.";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
