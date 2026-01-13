<?php
// logic/db.php

$host = 'localhost';
$port = '8889'; // WICHTIG bei MAMP!
$db   = 'royalfree_db';
$user = 'root';
$pass = 'root'; // Standard-Passwort bei MAMP ist meist 'root'
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Bei Fehler brechen wir ab
    die("Datenbank-Verbindung fehlgeschlagen: " . $e->getMessage());
}
?>