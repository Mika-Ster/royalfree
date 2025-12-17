<?php
require_once 'includes/auth.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pw = $_POST['password'] ?? '';
  if (login($email, $pw)) {
    header('Location: index.php'); exit;
  } else {
    $error = "Login fehlgeschlagen.";
  }
}
include 'includes/header.php';
?>
<div class="container py-5" style="max-width:540px;">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <h1 class="h4 text-center mb-3">Login</h1>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3"><label class="form-label">E-Mail</label><input name="email" type="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Passwort</label><input name="password" type="password" class="form-control" required></div>
        <div class="d-grid"><button class="btn btn-primary" type="submit">Anmelden</button></div>
      </form>
      <div class="text-center mt-3">Noch kein Benutzerkonto? <a href="register.php">Jetzt registrieren</a></div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
