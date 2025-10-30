<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Demo-Users in Session halten; später per DB ersetzen.
if (!isset($_SESSION['users'])) {
  $_SESSION['users'] = []; // leere Liste; erster registrierter User wird admin
}
if (!isset($_SESSION['suggestions'])) {
  $_SESSION['suggestions'] = []; // gesammelte Vorschläge
}

function currentUser() {
  return $_SESSION['user'] ?? null;
}

function isAdmin() {
  $u = currentUser();
  return $u && isset($u['role']) && $u['role'] === 'admin';
}

function requireLogin() {
  if (!currentUser()) {
    header('Location: login.php');
    exit;
  }
}

function findUserByEmail($email) {
  foreach ($_SESSION['users'] as $u) {
    if (strcasecmp($u['email'], $email) === 0) return $u;
  }
  return null;
}

function saveUser($user) {
  // Aktualisieren oder anhängen
  $found = false;
  foreach ($_SESSION['users'] as $i => $u) {
    if ($u['id'] === $user['id']) { $_SESSION['users'][$i] = $user; $found = true; break; }
  }
  if (!$found) $_SESSION['users'][] = $user;
}

function login($email, $password) {
  $u = findUserByEmail($email);
  if ($u && password_verify($password, $u['password'])) {
    $_SESSION['user'] = ['id'=>$u['id'], 'email'=>$u['email'], 'displayname'=>$u['displayname'], 'role'=>$u['role']];
    return true;
  }
  return false;
}

function logout() {
  unset($_SESSION['user']);
  header('Location: index.php');
  exit;
}
?>
