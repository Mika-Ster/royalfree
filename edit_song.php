<?php
require_once 'includes/header.php';
require_once 'logic/db.php';

if (!function_exists('isAdminLoggedIn') || !isAdminLoggedIn()) {
    header('Location: admin_login.php'); exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
if ($id <= 0) { header('Location: search.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $composer = trim($_POST['composer'] ?? '');
    $performer = trim($_POST['performer'] ?? '');
    $public_domain = isset($_POST['public_domain']) ? 1 : 0;

    $stmt = $pdo->prepare('UPDATE songs SET title = :title, year = :year, composer = :composer, performer = :performer, public_domain = :pd WHERE id = :id');
    $stmt->execute([
        ':title' => $title,
        ':year' => $year,
        ':composer' => $composer,
        ':performer' => $performer,
        ':pd' => $public_domain,
        ':id' => $id
    ]);

    header('Location: song.php?id=' . $id . '&saved=1'); exit();
}

$stmt = $pdo->prepare('SELECT * FROM songs WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$song = $stmt->fetch();
if (!$song) { header('Location: search.php'); exit(); }

?>
<h1 class="h3 mb-3">Song bearbeiten</h1>
<form method="post" class="card card-body shadow-sm" style="max-width:720px;">
  <input type="hidden" name="id" value="<?php echo htmlspecialchars($song['id']); ?>">
  <div class="mb-3"><label class="form-label">Titel</label><input name="title" class="form-control" value="<?php echo htmlspecialchars($song['title']); ?>" required></div>
  <div class="mb-3"><label class="form-label">Jahr</label><input name="year" type="number" class="form-control" value="<?php echo htmlspecialchars($song['year']); ?>"></div>
  <div class="mb-3"><label class="form-label">Komponist/in</label><input name="composer" class="form-control" value="<?php echo htmlspecialchars($song['composer']); ?>"></div>
  <div class="mb-3"><label class="form-label">Interpret/in</label><input name="performer" class="form-control" value="<?php echo htmlspecialchars($song['performer']); ?>"></div>
  <div class="mb-3 form-check"><input type="checkbox" id="public_domain" name="public_domain" class="form-check-input" <?php echo !empty($song['public_domain']) ? 'checked' : ''; ?>><label for="public_domain" class="form-check-label">gemeinfrei</label></div>
  <div class="d-flex gap-2"><button class="btn btn-primary">Speichern</button><a href="song.php?id=<?php echo urlencode($song['id']); ?>" class="btn btn-outline-secondary">Abbrechen</a></div>
</form>
<?php include 'includes/footer.php'; ?>
