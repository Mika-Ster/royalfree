<?php include 'includes/header.php'; include 'includes/auth.php'; $msg=''; $err='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $e = trim($_POST['email'] ?? '');
    $p = trim($_POST['password'] ?? '');
    $n = trim($_POST['displayname'] ?? '');
    if (!$e || !$p || !$n) { $err = 'Bitte alle Felder ausfüllen.'; }
    elseif (register($e, $p, $n)) { $msg = 'Registrierung erfolgreich. Bitte einloggen.'; }
    else { $err = 'E-Mail bereits registriert.'; }
} ?>