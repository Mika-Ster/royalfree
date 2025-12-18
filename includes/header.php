<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../logic/auth.php';
?>
<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="royalfree.com – Urheberrechts-Checker für Musik" />
  <link rel="icon" href="assets/img/logo.png" type="image/png+xml">
  <title>royalfree.com</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="hero d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
      <img src="assets/img/logo.png" alt="Royalfree Logo" width="70">
      <span class="fw-bold">royalfree<span class="text-primary">.com</span></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsMain" aria-controls="navbarsMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarsMain">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="search.php">Suche</a></li>
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
          <li class="nav-item"><a class="nav-link" href="upload.php">CSV Upload</a></li>
          <li class="nav-item ms-2"><a class="btn btn-warning" href="admin_logout.php">Logout (Admin)</a></li>
        <?php elseif (currentUser()): ?>
          <?php $__u = currentUser(); ?>
          <li class="nav-item d-flex align-items-center ms-3"><span class="small text-muted">Eingeloggt als <strong><?php echo htmlspecialchars($__u['displayname'] ?? $__u['email']); ?></strong></span></li>
          <li class="nav-item ms-2"><a class="btn btn-warning" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-outline-primary ms-lg-2" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Registrieren</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="flex-fill container">
