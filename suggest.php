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


// Ensure suggestions array exists
if (!isset($_SESSION['suggestions']) || !is_array($_SESSION['suggestions'])) {
  $_SESSION['suggestions'] = [];
}

$nextId = count($_SESSION['suggestions']) + 1;

$_SESSION['suggestions'][] = [
  'id' => $nextId,
  'user_id' => $user['id'],
  'song_id' => $song_id,
  'text' => $text,
  'created_at' => date('c')
];

header('Location: song.php?id='.$song_id.'&sent=1');
exit;
?>
