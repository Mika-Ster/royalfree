<?php
require_once 'logic/auth.php';

$info = null; $error = null;
include 'logic/registration_form';
?>

<?php include 'includes/header.php'; ?>
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
