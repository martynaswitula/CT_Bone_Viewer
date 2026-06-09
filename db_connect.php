<?php
$db_path = __DIR__ . '/database/ct_viewer.db';

if (!is_dir(__DIR__ . '/database')) {
    mkdir(__DIR__ . '/database', 0777, true);
}

try {
    $conn = new PDO('sqlite:' . $db_path);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Tworzenie tabeli jeśli nie istnieje
    $conn->exec("CREATE TABLE IF NOT EXISTS badania_ct (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        patient_id TEXT NOT NULL,
        anatomy_type TEXT NOT NULL,
        file_name TEXT,
        notes TEXT,
        preview_path TEXT,
        created_at DATETIME DEFAULT (datetime('now'))
    )");
    
} catch(PDOException $e) {
    die("Błąd bazy danych: " . $e->getMessage());
}
?>