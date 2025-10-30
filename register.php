<?php
require_once 'includes/auth.php';

$info = null; $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pw = $_POST['password'] ?? '';
  $display = trim($_POST['displayname'] ?? '');
  if ($email === '' || $pw === '' || $display === '') {
    $error = "Bitte alle Felder ausfüllen.";
  } elseif (findUserByEmail($email)) {
    $error = "E-Mail bereits registriert.";
  } else {
    $role = empty($_SESSION['users']) ? 'admin' : 'user'; // erster User wird Admin
    $user = [
      'id' => count($_SESSION['users']) + 1,
      'email' => $email,
      'password' => password_hash($pw, PASSWORD_BCRYPT),
      'displayname' => $display,
      'role' => $role
    ];
    saveUser($user);
    $info = "Registrierung erfolgreich. Bitte einloggen.";
  }
}
include 'includes/header.php';
?>
<div class="container py-5" style="max-width:540px;">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <h1 class="h4 text-center mb-3">Registrieren</h1>
      <?php if ($info): ?><div class="alert alert-success"><?php echo htmlspecialchars($info); ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3"><label class="form-label">E-Mail</label><input name="email" type="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Passwort</label><input name="password" type="password" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Anzeigename</label><input name="displayname" type="text" class="form-control" required></div>
        <div class="d-grid"><button class="btn btn-primary" type="submit">Konto anlegen</button></div>
      </form>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
