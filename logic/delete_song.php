<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../search.php'); exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM songs WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: ../search.php?deleted=1'); exit();
