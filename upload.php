<?php
require_once 'includes/header.php';
require_once 'logic/auth.php';
// Admin-only page: verify admin session (similar to admin.php)
if (!isset($_SESSION['admin_logged_in']) ||
  $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] ||
  $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']) {
  $adminUser = 'admin@technikum-wien.at';
  if (isset($_COOKIE['remember_admin']) && $_COOKIE['remember_admin'] === hash('sha256', $adminUser)) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
  } else {
    header('Location: admin_login.php'); exit();
  }
}

$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) { mkdir($uploadDir, 0777, true); }

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['datafile'])) {
  $file = $_FILES['datafile'];
  if ($file['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['csv', 'json'])) {
      $target = $uploadDir . basename($file['name']);
      move_uploaded_file($file['tmp_name'], $target);
      $message = "Datei „" . htmlspecialchars($file['name']) . "“ wurde erfolgreich hochgeladen.";
    } else {
      $message = "Nur CSV- oder JSON-Dateien sind erlaubt.";
    }
  } else {
    $message = "Fehler beim Upload.";
  }
}
?>
<h1 class="h3 mb-3">Admin: Datei-Upload</h1>
<?php if ($message): ?><div class="alert alert-info"><?php echo $message; ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="mb-4">
  <div class="mb-3">
    <label for="datafile" class="form-label">Datei auswählen (CSV oder JSON)</label>
    <input type="file" class="form-control" id="datafile" name="datafile" accept=".csv,.json" required>
  </div>
  <button type="submit" class="btn btn-primary">Hochladen</button>
</form>

<h2 class="h5 mt-4">Bereits hochgeladene Dateien</h2>
<ul class="list-group">
  <?php
  $files = array_diff(scandir($uploadDir), ['.', '..']);
  if (empty($files)) {
    echo '<li class="list-group-item text-muted">Keine Dateien vorhanden.</li>';
  } else {
    foreach ($files as $f) {
      echo '<li class="list-group-item d-flex justify-content-between align-items-center">'
         . htmlspecialchars($f)
         . '<span class="text-muted small">' . date("d.m.Y H:i", filemtime($uploadDir.$f)) . '</span></li>';
    }
  }
  ?>
</ul>
<?php include 'includes/footer.php'; ?>
