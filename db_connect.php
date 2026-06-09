<?php
$db_path = '/home/site/wwwroot/database/ct_viewer.db';

if (!is_dir('/home/site/wwwroot/database')) {
    mkdir('/home/site/wwwroot/database', 0777, true);
}

try {
    $conn = new PDO('sqlite:' . $db_path);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
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