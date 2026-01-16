<?php
// Wir laden die Datenbankverbindung
require_once __DIR__ . '/../logic/db.php';

try {
    // 1. SQL Abfrage vorbereiten
    $stmt = $pdo->query("SELECT * FROM songs");
    
    // 2. Alle Ergebnisse holen
    $rawSongs = $stmt->fetchAll();
    
    // 3. Typ-Anpassung (Datenbank liefert oft Strings, PHP braucht int/bool)
    $SONGS = [];
    foreach ($rawSongs as $row) {
        // public_domain kommt als 0 oder 1 aus der DB, wir machen true/false draus
        $row['public_domain'] = (bool)$row['public_domain'];
        $row['year'] = (int)$row['year'];
        $row['id'] = (int)$row['id'];
        
        $SONGS[] = $row;
    }

} catch (PDOException $e) {
    // Fallback, falls DB crasht (damit die Seite nicht weiß bleibt)
    $SONGS = [];
    error_log("DB Fehler in data.php: " . $e->getMessage());
}
?>