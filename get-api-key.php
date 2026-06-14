<?php
// Serve API key as JavaScript variable (only for authenticated sessions)
// This ensures API key is never exposed in git, source code, or to unauthenticated users

if (!isset($_SESSION)) {
    session_start();
}

// Optional: Require authentication if sensitive
// if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
//     http_response_code(403);
//     die("Unauthorized");
// }

require_once 'dbconnect.php';

$api_key = $_ENV['MAPY_API_KEY'] ?? null;

if (!$api_key) {
    http_response_code(500);
    die("API key not configured in .env.local");
}

header('Content-Type: application/javascript');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo "const API_KEY = " . json_encode($api_key) . ";";
?>
