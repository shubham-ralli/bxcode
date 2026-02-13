<?php
// Turn on all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Increase limits for this script
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');

$target_dir = "../resources/views/themes/"; // Adjust relative path from public/
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["fileToUpload"])) {
        $file = $_FILES["fileToUpload"];

        echo "<h3>Debug Info:</h3>";
        echo "File Name: " . $file["name"] . "<br>";
        echo "File Size: " . $file["size"] . " bytes<br>";
        echo "Temp Path: " . $file["tmp_name"] . "<br>";
        echo "Error Code: " . $file["error"] . "<br>";

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $message = "Upload Error Code: " . $file["error"];
        } else {
            // Try to move it to themes
            $target_file = $target_dir . basename($file["name"]);

            // Unzip test
            $zip = new ZipArchive;
            if ($zip->open($file["tmp_name"]) === TRUE) {
                // Extract to a temp folder first to check structure
                $extractPath = $target_dir . pathinfo($file["name"], PATHINFO_FILENAME);

                if (!file_exists($extractPath)) {
                    mkdir($extractPath, 0755, true);
                }

                $zip->extractTo($extractPath);
                $zip->close();
                $message = "<div style='color:green'>SUCCESS: Extracted to " . realpath($extractPath) . "</div>";
            } else {
                $message = "<div style='color:red'>FAILED to open ZIP file.</div>";
            }
        }
    } else {
        $message = "No file received.";
    }
}
?>

<!DOCTYPE html>
<html>

<body>

    <h2>Theme Upload Debugger (Bypasses Laravel)</h2>
    <p>Use this to test if the server allows uploads at all.</p>

    <form action="debug_upload.php" method="post" enctype="multipart/form-data">
        Select ZIP to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload ZIP" name="submit">
    </form>

    <?php echo $message; ?>

</body>

</html>