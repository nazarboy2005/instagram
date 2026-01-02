<?php

// Router for PHP built-in server
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove trailing slash except for root
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// Direct file access - serve static files
$static_extensions = ['html', 'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'woff', 'woff2'];
$extension = pathinfo($path, PATHINFO_EXTENSION);

if (in_array(strtolower($extension), $static_extensions)) {
    $file_path = __DIR__ . $path;
    if (file_exists($file_path)) {
        return false;
    }
}

// PHP files - serve directly
if ($extension === 'php') {
    $file_path = __DIR__ . $path;
    if (file_exists($file_path)) {
        return false;
    }
}

// Route Instagram-like URLs to index.php
if (preg_match('#^/(p|reel|tv)/([a-zA-Z0-9_-]+)$#', $path)) {
    require __DIR__ . '/index.php';
    return true;
}

// Root path
if ($path === '/' || $path === '') {
    require __DIR__ . '/index.php';
    return true;
}

// 404 for unknown paths
http_response_code(404);
echo "404 Not Found";
return true;

?>
