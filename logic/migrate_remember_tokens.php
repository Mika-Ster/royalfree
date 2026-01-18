<?php
// Migration script: import logic/remember_tokens.json into DB table remember_tokens
require_once __DIR__ . '/db.php';

$path = __DIR__ . '/remember_tokens.json';
if (!file_exists($path)) {
    echo "No remember_tokens.json found at $path\n"; exit(1);
}

$json = file_get_contents($path);
$data = json_decode($json, true);
if (!is_array($data)) { echo "Invalid JSON or empty file.\n"; exit(1); }

$insert = $pdo->prepare('INSERT INTO remember_tokens (token_hash, user_email, type, expires, user_agent, ip) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE expires = VALUES(expires), user_agent = VALUES(user_agent), ip = VALUES(ip)');
$count = 0;
foreach ($data as $tokenHash => $rec) {
    $email = $rec['email'] ?? null;
    $type = $rec['type'] ?? 'user';
    $expires = isset($rec['expires']) ? (int)$rec['expires'] : (time() + 60*60*24*30);
    $ua = $rec['user_agent'] ?? null;
    $ip = $rec['ip'] ?? null;
    $insert->execute([$tokenHash, $email, $type, $expires, $ua, $ip]);
    $count++;
}

echo "Imported $count remember tokens into database.\n";
