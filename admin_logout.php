<?php
$_SESSION = [];
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
if (isset($_COOKIE['remember_admin'])) {
  setcookie('remember_admin', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
  ]);
  unset($_COOKIE['remember_admin']);
}
header('Location: admin_login.php'); exit();
