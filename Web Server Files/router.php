<?php

// Router for PHP built-in server
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Serve static files directly
$static_extensions = ['html', 'css', 'js', 'png', 'jpg', 'ico', 'svg'];
$extension = pathinfo($path, PATHINFO_EXTENSION);

if (in_array($extension, $static_extensions) && file_exists(__DIR__ . $path)) {
    return false; // Let PHP serve the file
}

// Handle PHP files
if ($extension === 'php' && file_exists(__DIR__ . $path)) {
    return false;
}

// Route Instagram-like URLs to index.php
if (preg_match('#^/(p|reel|tv)/([a-zA-Z0-9_-]+)/?$#', $path)) {
    include __DIR__ . '/index.php';
    return true;
}

// Default routing
if ($path === '/' || !file_exists(__DIR__ . $path)) {
    include __DIR__ . '/index.php';
    return true;
}

return false;

?>
