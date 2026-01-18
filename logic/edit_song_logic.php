<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

if (!function_exists('isAdminLoggedIn') || !isAdminLoggedIn()) {
    header('Location: ../admin_login.php'); exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
if ($id <= 0) { header('Location: search.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $composer = trim($_POST['composer'] ?? '');
    $performer = trim($_POST['performer'] ?? '');
    $public_domain = isset($_POST['public_domain']) ? 1 : 0;

    $stmt = $pdo->prepare('UPDATE songs SET title = :title, year = :year, composer = :composer, performer = :performer, public_domain = :pd WHERE id = :id');
    $stmt->execute([
        ':title' => $title,
        ':year' => $year,
        ':composer' => $composer,
        ':performer' => $performer,
        ':pd' => $public_domain,
        ':id' => $id
    ]);

    header('Location: ../song.php?id=' . $id . '&saved=1'); exit();
}

$stmt = $pdo->prepare('SELECT * FROM songs WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$song = $stmt->fetch();
if (!$song) { header('Location: ../search.php'); exit(); }
?>