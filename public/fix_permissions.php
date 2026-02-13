<?php

function recursiveChmod($path, $filePerm = 0666, $dirPerm = 0777)
{
    if (!file_exists($path)) {
        return "Path $path does not exist.<br>";
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $results = "";

    // Change the root path permissions
    if (chmod($path, $dirPerm)) {
        $results .= "Changed $path to 0777<br>";
    } else {
        $results .= "Failed to change $path<br>";
    }

    foreach ($iterator as $item) {
        $target = $item->getPathname();
        if ($item->isDir()) {
            if (chmod($target, $dirPerm)) {
                // $results .= "Changed Dir: $target<br>";
            } else {
                $results .= "Failed Dir: $target<br>";
            }
        } else {
            if (chmod($target, $filePerm)) {
                // $results .= "Changed File: $target<br>";
            } else {
                $results .= "Failed File: $target<br>";
            }
        }
    }
    return $results;
}

echo "<h1>Fixing Permissions...</h1>";
echo recursiveChmod(__DIR__ . '/../storage');
echo recursiveChmod(__DIR__ . '/../bootstrap/cache');
echo "<h2>Done.</h2>";
