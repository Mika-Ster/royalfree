<?php
require_once __DIR__ . '/../logic/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM songs");
    
    $rawSongs = $stmt->fetchAll();
    
    $SONGS = [];
    foreach ($rawSongs as $row) {
        $row['public_domain'] = (bool)$row['public_domain'];
        $row['year'] = (int)$row['year'];
        $row['id'] = (int)$row['id'];
        
        $SONGS[] = $row;
    }

} catch (PDOException $e) {
    $SONGS = [];
    error_log("DB Fehler in data.php: " . $e->getMessage());
}
?>