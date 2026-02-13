<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "<h1>Media Debugger</h1>";

try {
    $mediaItems = Media::orderBy('id', 'desc')->take(10)->get();

    echo "<table border='1' cellpadding='5' style='border-collapse:collapse; width:100%;'>";
    echo "<tr>
            <th>ID</th>
            <th>Filename</th>
            <th>DB Path</th>
            <th>Computed Relative Path</th>
            <th>Exists on 'uploads' disk?</th>
            <th>Absolute Path (on disk)</th>
          </tr>";

    foreach ($mediaItems as $media) {
        $relativePath = str_replace(['/uploads/', 'uploads/'], '', $media->path);

        $existsOnDisk = Storage::disk('uploads')->exists($relativePath) ? 'YES' : 'NO';
        $absolutePath = Storage::disk('uploads')->path($relativePath);
        $realExists = file_exists($absolutePath) ? 'YES' : 'NO';

        $color = ($existsOnDisk === 'YES') ? 'green' : 'red';

        echo "<tr>";
        echo "<td>{$media->id}</td>";
        echo "<td>{$media->filename}</td>";
        echo "<td>{$media->path}</td>";
        echo "<td>{$relativePath}</td>";
        echo "<td style='color:$color'><strong>$existsOnDisk</strong></td>";
        echo "<td>{$absolutePath} <br> (File Exists: <strong>$realExists</strong>)</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
