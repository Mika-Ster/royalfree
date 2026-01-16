<?php
require_once 'includes/header.php';
require_once 'logic/db.php';

if (!function_exists('isAdminLoggedIn') || !isAdminLoggedIn()) {
    header('Location: admin_login.php'); exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: search.php'); exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM songs WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: search.php?deleted=1'); exit();
