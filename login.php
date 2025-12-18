<?php include 'includes/header.php'; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pw = trim($_POST['password'] ?? '');
    if (login($email, $pw)) { header('Location: index.php'); exit(); }
    else { $error = 'Ungültige Anmeldedaten.'; }
} ?>
<h2>Login</h2>
<form method="post" class="card card-body shadow-sm">
  <div class="mb-3">
    <label class="form-label" for="email">E-Mail</label>
    <input type="email" name="email" id="email" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label" for="password">Passwort</label>
    <input type="password" name="password" id="password" class="form-control" required>
  </div>
  <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="remember" name="remember">
    <label class="form-check-label" for="remember">Remember Me</label>
  </div>
  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Login</button>
    <a href="register.php" class="btn btn-outline-primary">Registrieren</a>
    <a href="index.php" class="btn btn-outline-secondary">Zurück</a>
  </div>
  <?php if ($error) echo "<div class='alert alert-danger mt-3'>$error</div>"; ?>
</form>
<?php include 'includes/footer.php'; ?>
