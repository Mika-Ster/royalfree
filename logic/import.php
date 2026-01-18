<?php
// royalfree/logic/import.php

/**
 * Verarbeitet den CSV Import
 * @param array $file Das $_FILES['datafile'] Array
 * @param PDO $pdo Die Datenbankverbindung
 * @param string $uploadDir Zielordner
 * @return array [success => bool, message => string]
 */
function processCsvImport($file, $pdo, $uploadDir) {
    // 1. Validierung
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => "Upload-Fehler Code: " . $file['error']];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'csv') {
        return ['success' => false, 'message' => "Bitte nur .csv Dateien hochladen."];
    }

    // 2. Upload
    if (!file_exists($uploadDir)) { mkdir($uploadDir, 0777, true); }
    $target = $uploadDir . basename($file['name']);
    
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return ['success' => false, 'message' => "Fehler beim Verschieben der Datei."];
    }

    // 3. Datenbank Import
    try {
        $handle = fopen($target, "r");
        if ($handle === FALSE) {
            return ['success' => false, 'message' => "Konnte Datei nicht öffnen."];
        }

        // SQL Prepare (Performance & Sicherheit)
        $sql = "INSERT INTO songs (title, year, public_domain, protection_until, composer, lyricist, performer) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Header Zeile überspringen
        fgetcsv($handle, 1000, ","); 
        
        $count = 0;
        // Zeile für Zeile durchgehen
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) >= 7) {
                // Mapping der Spalten laut deiner CSV Struktur
                $params = [
                    $data[0],       // Title
                    (int)$data[1],  // Year
                    (int)$data[2],  // Public Domain
                    $data[3],       // Protection
                    $data[4],       // Composer
                    $data[5],       // Lyricist
                    $data[6]        // Performer
                ];
                $stmt->execute($params);
                $count++;
            }
        }
        fclose($handle);
        return ['success' => true, 'message' => "Erfolg! <strong>$count Songs</strong> wurden importiert."];

    } catch (Exception $e) {
        return ['success' => false, 'message' => "DB Fehler: " . $e->getMessage()];
    }
}
?>