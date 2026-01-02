<?php

// Router for PHP built-in server
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove trailing slash except for root
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// List of PHP files that should be directly accessible
$allowed_php = ['capture.php', 'bot.php', 'setup_webhook.php', 'test_telegram.php'];

// Check if requesting a PHP file directly
$filename = basename($path);
if (in_array($filename, $allowed_php)) {
    $file_path = __DIR__ . '/' . $filename;
    if (file_exists($file_path)) {
        include $file_path;
        return true;
    }
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

// PHP files in root - serve directly
if ($extension === 'php') {
    $file_path = __DIR__ . $path;
    if (file_exists($file_path)) {
        include $file_path;
        return true;
    }
    // Try without path prefix
    $file_path = __DIR__ . '/' . $filename;
    if (file_exists($file_path)) {
        include $file_path;
        return true;
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

// Try to find the file
$file_path = __DIR__ . $path;
if (file_exists($file_path) && is_file($file_path)) {
    return false;
}

// 404 for unknown paths
http_response_code(404);
echo "404 Not Found";
return true;

?>
