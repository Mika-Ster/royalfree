<?php
require_once 'includes/header.php';
require_once 'includes/data.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];
if ($q !== '') {
  foreach ($SONGS as $s) {
    $hay = strtolower(($s['title'] ?? '') . ' ' . ($s['composer'] ?? '') . ' ' . ($s['lyricist'] ?? '') . ' ' . ($s['performer'] ?? ''));
    if (strpos($hay, strtolower($q)) !== false) {
      $results[] = $s;
    }
  }
} else {
  $results = $SONGS;
}
?>
<h1 class="h3 mb-3">Suche</h1>
<form class="row g-2" action="search.php" method="get">
  <div class="col-sm-9"><input name="q" type="search" value="<?php echo htmlspecialchars($q); ?>" class="form-control" placeholder="Suchbegriff"></div>
  <div class="col-sm-3 d-grid"><button class="btn btn-primary">Suchen</button></div>
</form>
<p class="text-muted small mt-2">Suchbegriff: <strong><?php echo $q === '' ? '—' : htmlspecialchars($q); ?></strong></p>

<div class="table-responsive mt-3">
  <table class="table align-middle">
    <thead><tr><th>Titel</th><th>Jahr</th><th>Status</th><th>Komponist:in</th><th>Interpret:in</th></tr></thead>
    <tbody>
      <?php if (empty($results)): ?>
        <tr><td colspan="5" class="text-center text-muted py-4">Keine Ergebnisse</td></tr>
      <?php else: foreach ($results as $s): ?>
        <tr>
          <td><a class="text-decoration-none" href="song.php?id=<?php echo urlencode($s['id']); ?>"><?php echo htmlspecialchars($s['title']); ?></a></td>
          <td><?php echo htmlspecialchars($s['year'] ?? '—'); ?></td>
          <td>
            <?php if (!empty($s['public_domain'])): ?>
              <span class="badge text-bg-success">gemeinfrei</span>
            <?php else: ?>
              <span class="badge text-bg-warning text-dark">geschützt</span>
            <?php endif; ?>
          </td>
          <td><?php echo htmlspecialchars($s['composer'] ?? '—'); ?></td>
          <td><?php echo htmlspecialchars($s['performer'] ?? '—'); ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php include 'includes/footer.php'; ?>
