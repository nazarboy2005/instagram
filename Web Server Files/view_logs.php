<?php
// Simple password protection
$password = "your_secure_password_here";

if(!isset($_GET['pass']) || $_GET['pass'] !== $password) {
    die("Access Denied");
}

$log_file = "credentials_log.txt";

if(file_exists($log_file)) {
    echo "<html><head><title>Backup Logs</title>";
    echo "<style>body{background:#000;color:#0f0;font-family:monospace;padding:20px;}</style>";
    echo "</head><body>";
    echo "<h2>Backup Credential Logs</h2>";
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($log_file));
    echo "</pre>";
    echo "</body></html>";
} else {
    echo "No logs found";
}
?>
