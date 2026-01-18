<?php
require_once __DIR__ . '/logic/auth.php';
require_once __DIR__ . '/logic/data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
if (!currentUser()) { header('Location: login.php'); exit; }

$user = currentUser();
$song_id = isset($_POST['song_id']) ? (int)$_POST['song_id'] : 0;
$text = isset($_POST['text']) ? trim($_POST['text']) : '';

if ($song_id <= 0 || $text === '') {
  header('Location: song.php?id='.$song_id.'&sent=0'); exit;
}

try {
  $stmt = $pdo->prepare('INSERT INTO suggestions (user_id, song_id, text, created_at) VALUES (?, ?, ?, ?)');
  $stmt->execute([(int)$user['id'], $song_id, $text, date('Y-m-d H:i:s')]);
} catch (Exception $e) {
  header('Location: song.php?id='.$song_id.'&sent=0'); exit;
}

header('Location: song.php?id='.$song_id.'&sent=1');
exit;
?>
