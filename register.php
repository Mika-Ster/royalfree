<?php require_once 'logic/registration_form.php'; ?>
<h2>Registrieren</h2>
<form method="post" class="card card-body shadow-sm">
  <div class="mb-3">
    <label class="form-label" for="displayname">Name</label>
    <input type="text" name="displayname" id="displayname" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label" for="email">E-Mail</label>
    <input type="email" name="email" id="email" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label" for="password">Passwort</label>
    <input type="password" name="password" id="password" class="form-control" required>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-primary">Registrieren</button>
    <a href="login.php" class="btn btn-outline-secondary">Zurück zum Login</a>
  </div>
  <?php if ($msg) echo "<div class='alert alert-success mt-3'>$msg</div>"; ?>
  <?php if ($err) echo "<div class='alert alert-danger mt-3'>$err</div>"; ?>
</form>
<?php include 'includes/footer.php'; ?>
