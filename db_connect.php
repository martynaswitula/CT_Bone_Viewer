<?php
$db_path = '/home/site/wwwroot/database/ct_viewer.db';

if (!is_dir('/home/site/wwwroot/database')) {
    mkdir('/home/site/wwwroot/database', 0777, true);
}

try {
    $conn = new PDO('sqlite:' . $db_path);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    $conn->exec("CREATE TABLE IF NOT EXISTS lekarze (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        imie TEXT NOT NULL,
        nazwisko TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        haslo TEXT NOT NULL,
        rola TEXT DEFAULT 'lekarz',
        created_at DATETIME DEFAULT (datetime('now'))
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS badania_ct (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        lekarz_id INTEGER NOT NULL,
        patient_id TEXT NOT NULL,
        anatomy_type TEXT NOT NULL,
        file_name TEXT,
        notes TEXT,
        preview_path TEXT,
        created_at DATETIME DEFAULT (datetime('now')),
        FOREIGN KEY (lekarz_id) REFERENCES lekarze(id)
    )");

    // Tworzenie admina jeśli nie istnieje
    $admin = $conn->query("SELECT id FROM lekarze WHERE email = 'admin@ctviewer.pl'")->fetch();
    if (!$admin) {
        $haslo = password_hash('Admin2026!', PASSWORD_DEFAULT);
        $conn->exec("INSERT INTO lekarze (imie, nazwisko, email, haslo, rola) 
                     VALUES ('Admin', 'System', 'admin@ctviewer.pl', '$haslo', 'admin')");
    }
    
} catch(PDOException $e) {
    die("Błąd bazy danych: " . $e->getMessage());
}
?>