<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';
requireLogin();
if (!isAdmin()) { echo '<div class="alert alert-danger mt-3">Kein Zugriff.</div>'; include 'includes/footer.php'; exit; }

$sugs = $_SESSION['suggestions'] ?? [];
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
