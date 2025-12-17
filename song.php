<?php
require_once 'includes/header.php';
require_once 'includes/data.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$song = null;
foreach ($SONGS as $s) { if ((int)$s['id'] === $id) { $song = $s; break; } }
if (!$song) { echo '<div class="alert alert-warning">Song nicht gefunden.</div>'; include 'includes/footer.php'; exit; }
?>
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
  <div>
    <h1 class="h3 mb-1"><?php echo htmlspecialchars($song['title']); ?></h1>
    <?php if (!empty($song['public_domain'])): ?>
      <span class="badge text-bg-success">gemeinfrei</span>
    <?php else: ?>
      <span class="badge text-bg-warning text-dark">geschützt</span>
    <?php endif; ?>
  </div>
  <a href="search.php" class="btn btn-outline-secondary">Zurück zur Suche</a>
</div>

<div class="row g-4 mt-1">
  <div class="col-md-6">
    <div class="card border-0">
      <div class="card-body">
        <h2 class="h5">Werkdaten</h2>
        <div class="text-secondary">
          <div><span class="text-muted">Erscheinungsjahr:</span> <?php echo htmlspecialchars($song['year'] ?? '—'); ?></div>
          <div><span class="text-muted">Schutzende*:</span> <?php echo htmlspecialchars($song['protection_until'] ?? '—'); ?></div>
        </div>
        <hr>
        <div class="text-secondary">
          <div><span class="text-muted">Komponist/in:</span> <?php echo htmlspecialchars($song['composer'] ?? '—'); ?></div>
          <div><span class="text-muted">Texter/in:</span> <?php echo htmlspecialchars($song['lyricist'] ?? '—'); ?></div>
          <div><span class="text-muted">Interpret/in:</span> <?php echo htmlspecialchars($song['performer'] ?? '—'); ?></div>
        </div>
        <p class="small text-muted mt-3 mb-0">* Überschlägige Angabe. Keine Rechtsberatung.</p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0">
      <div class="card-body">
        <h2 class="h5 mb-3">Änderung vorschlagen</h2>
        <?php if (!isset($_SESSION['user'])): ?>
          <div class="alert alert-warning mb-3">Bitte einloggen, um Vorschläge zu senden.</div>
          <a class="btn btn-primary" href="login.php">Zum Login</a>
        <?php else: ?>
          <?php if (isset($_GET['sent']) && $_GET['sent']==='1'): ?>
            <div class="alert alert-success">Danke. Dein Vorschlag wurde gespeichert (Demo).</div>
          <?php endif; ?>
          <form action="suggest.php" method="post">
            <input type="hidden" name="song_id" value="<?php echo (int)$song['id']; ?>">
            <div class="mb-3">
              <label class="form-label">Beschreibung</label>
              <textarea name="text" class="form-control" rows="4" placeholder="Was soll angepasst werden? Quellen, Korrekturen, Ergänzungen …" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Vorschlag senden</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
