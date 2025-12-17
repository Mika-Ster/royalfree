<?php include 'includes/header.php'; $err=''; $adminUser='admin@technikum-wien.at'; $adminPass='admin123';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    $rem = isset($_POST['remember']);
    if ($u === $adminUser && $p === $adminPass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        if ($rem) {
            $t = hash('sha256', $adminUser);
            setcookie('remember_admin', $t, [
                'expires' => time() + 60*60*24*30,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
        header('Location: admin.php'); exit();
    } else { $err = 'Falsche Admin-Daten.'; }
} ?>
<h2>Admin Login</h2>
<form method="post" class="card card-body shadow-sm">
  <div class="mb-3">
    <label class="form-label" for="username">Admin E-Mail</label>
    <input type="text" name="username" id="username" class="form-control" value="admin@technikum-wien.at" required>
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
    <button class="btn btn-dark">Login</button>
    <a href="index.php" class="btn btn-outline-secondary">Zurück</a>
  </div>
  <?php if ($err) echo "<div class='alert alert-danger mt-3'>$err</div>"; ?>
</form>
<?php include 'includes/footer.php'; ?>
