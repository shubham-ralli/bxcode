<?php
// Test script to send DELETE request via cURL

$id = 1; // Arbitrary ID, doesn't matter if it exists for checking 405 vs 404
$url = "http://localhost/bxcode-cms/public/bx-admin/media/$id";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

// Faking CSRF and Auth is hard without session cookies,
// but 405 should happen BEFORE Auth if it's a routing issue?
// Actually, Auth middleware runs before controller.
// But MethodNotAllowed might be thrown by Router before Middleware?
// Let's see what happens.

echo "<h1>Testing DELETE to $url</h1>";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "HTTP Code: $httpCode <br>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

curl_close($ch);
