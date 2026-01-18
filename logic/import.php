<?php

function processCsvImport($file, $pdo, $uploadDir) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => "Upload-Fehler Code: " . $file['error']];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'csv') {
        return ['success' => false, 'message' => "Bitte nur .csv Dateien hochladen."];
    }

    if (!file_exists($uploadDir)) { mkdir($uploadDir, 0777, true); }
    $target = $uploadDir . basename($file['name']);
    
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return ['success' => false, 'message' => "Fehler beim Verschieben der Datei."];
    }

    try {
        $handle = fopen($target, "r");
        if ($handle === FALSE) {
            return ['success' => false, 'message' => "Konnte Datei nicht öffnen."];
        }

        $sql = "INSERT INTO songs (title, year, public_domain, protection_until, composer, lyricist, performer) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        fgetcsv($handle, 1000, ","); 
        
        $count = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) >= 7) {
                $params = [
                    $data[0],       
                    (int)$data[1],  
                    (int)$data[2],  
                    $data[3],       
                    $data[4],       
                    $data[5],       
                    $data[6]        
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