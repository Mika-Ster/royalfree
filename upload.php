<?php
require_once 'includes/header.php';
require_once 'logic/auth.php';
// WICHTIG: Die Datenbank-Verbindung laden
require_once 'logic/db.php'; 

// Admin-Sicherheitscheck (wie gehabt)
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
$errorMsg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['datafile'])) {
  $file = $_FILES['datafile'];
  
  if ($file['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Wir erlauben nur CSV für den Import
    if ($ext === 'csv') {
      $target = $uploadDir . basename($file['name']);
      
      if (move_uploaded_file($file['tmp_name'], $target)) {
        // --- START IMPORT LOGIK ---
        try {
            // Datei zum Lesen öffnen
            $handle = fopen($target, "r");
            if ($handle !== FALSE) {
                
                // SQL vorbereiten (Platzhalter ?)
                $sql = "INSERT INTO songs (title, year, public_domain, protection_until, composer, lyricist, performer) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                // Erste Zeile (Überschriften) überspringen
                fgetcsv($handle, 1000, ","); 
                
                $importedCount = 0;
                
                // Zeile für Zeile durchgehen
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Prüfen, ob die Zeile genug Spalten hat (wir brauchen 7)
                    if (count($data) >= 7) {
                        // Daten zuweisen (Reihenfolge muss mit CSV übereinstimmen!)
                        $title = $data[0];
                        $year = (int)$data[1];
                        $public_domain = (int)$data[2]; // 0 oder 1
                        $protection = $data[3];
                        $composer = $data[4];
                        $lyricist = $data[5];
                        $performer = $data[6];
                        
                        // In DB speichern
                        $stmt->execute([$title, $year, $public_domain, $protection, $composer, $lyricist, $performer]);
                        $importedCount++;
                    }
                }
                fclose($handle);
                $message = "Datei erfolgreich hochgeladen und <strong>$importedCount Songs</strong> importiert!";
            } else {
                $errorMsg = "Konnte die Datei nicht öffnen.";
            }
        } catch (Exception $e) {
            $errorMsg = "Datenbank-Fehler beim Import: " . $e->getMessage();
        }
        // --- ENDE IMPORT LOGIK ---
        
      } else {
        $errorMsg = "Fehler beim Verschieben der Datei.";
      }
    } else {
        $errorMsg = "Bitte lade eine <strong>.csv</strong> Datei hoch (JSON noch nicht implementiert).";
    }
  } else {
    $errorMsg = "Upload-Fehler Code: " . $file['error'];
  }
}
?>

<h1 class="h3 mb-3">Admin: CSV Import</h1>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($errorMsg): ?>
    <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
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
  // Ordner auslesen
  $files = array_diff(scandir($uploadDir), ['.', '..']);
  if (empty($files)) {
    echo '<li class="list-group-item text-muted">Keine Dateien im Archiv.</li>';
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