<?php
// 1. Setup & Abhängigkeiten laden
require_once 'includes/header.php'; // Startet Session
require_once 'logic/auth.php';      // Lädt Auth-Funktionen
require_once 'logic/db.php';        // Lädt $pdo Datenbankverbindung
require_once 'logic/import.php';    // Lädt die Import-Logik

// 2. Sicherheitscheck (Das ist der Ersatz für den langen Block vorher)
requireAdmin(); 

// 3. Controller-Logik (Verarbeitung)
$msg = null;
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['datafile'])) {
    $uploadDir = __DIR__ . '/uploads/';
    
    // Wir rufen nur noch die Funktion auf - kein Spaghetti-Code mehr hier!
    $result = processCsvImport($_FILES['datafile'], $pdo, $uploadDir);
    
    $msg = $result['message'];
    $msgType = $result['success'] ? 'success' : 'danger';
}
?>

<h1 class="h3 mb-3">Admin: CSV Import</h1>

<?php if ($msg): ?>
    <div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div>
<?php endif; ?>

<div class="card card-body shadow-sm mb-4">
    <h5 class="card-title">Neue Songs importieren</h5>
    <p class="text-muted small">
        Die CSV-Datei muss folgende Spaltenreihenfolge haben (mit Überschrift in Zeile 1):<br>
        <code>Titel, Jahr, Gemeinfrei (1=Ja/0=Nein), Schutzdauer, Komponist, Texter, Interpret</code>
    </p>
    
    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="datafile" class="form-label">CSV-Datei auswählen</label>
        <input type="file" class="form-control" id="datafile" name="datafile" accept=".csv" required>
      </div>
      <button type="submit" class="btn btn-primary">Hochladen & Importieren</button>
    </form>
</div>

<h2 class="h5 mt-4">Bereits hochgeladene Dateien</h2>
<ul class="list-group">
  <?php
  $dir = __DIR__ . '/uploads/';
  if (is_dir($dir)) {
      $files = array_diff(scandir($dir), ['.', '..']);
      // Filtere versteckte Dateien und zeige sie an
      foreach ($files as $f) {
          if (strpos($f, '.') !== 0) { 
              echo '<li class="list-group-item d-flex justify-content-between align-items-center">'
                 . htmlspecialchars($f)
                 . '<span class="text-muted small">' . date("d.m.Y H:i", filemtime($dir.$f)) . '</span></li>';
          }
      }
  }
  ?>
</ul>

<?php include 'includes/footer.php'; ?>