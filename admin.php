<?php include 'includes/header.php';
require_once 'logic/db.php';
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
$sugs = [];
try {
  $query = 'SELECT s.id, s.song_id, s.user_id, s.text, s.created_at, u.displayname
        FROM suggestions s
        LEFT JOIN users u ON u.id = s.user_id
        ORDER BY s.created_at DESC';
  $stmt = $pdo->query($query);
  $sugs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $sugs = [];
}
?>
<h1 class="h3 mb-3">Admin: Vorschläge</h1>
<?php if (empty($sugs)): ?>
  <div class="alert alert-info">Keine Vorschläge vorhanden.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>ID</th><th>Song-ID</th><th>User-ID</th><th>Text</th><th>Datum</th></tr></thead>
      <tbody>
      <?php foreach ($sugs as $v): ?>
        <tr>
          <td><?php echo (int)$v['id']; ?></td>
          <td><?php echo (int)$v['song_id']; ?></td>
          <td><?php echo (int)$v['user_id']; ?></td>
          <td><?php echo nl2br(htmlspecialchars($v['text'])); ?></td>
          <td><?php echo htmlspecialchars($v['created_at']); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>