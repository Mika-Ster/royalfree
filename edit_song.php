<?php
require_once 'includes/header.php';
require_once 'logic/edit_song_logic.php';

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
