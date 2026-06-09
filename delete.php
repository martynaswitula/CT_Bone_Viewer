<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['lekarz_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Sprawdź czy badanie należy do zalogowanego lekarza
        $stmt = $conn->prepare("SELECT preview_path, lekarz_id FROM badania_ct WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            die("Badanie nie istnieje.");
        }

        if ($row['lekarz_id'] != $_SESSION['lekarz_id']) {
            die("Brak uprawnień do usunięcia tego badania.");
        }

        if (!empty($row['preview_path']) && file_exists($row['preview_path'])) {
            unlink($row['preview_path']);
        }

        $stmt = $conn->prepare("DELETE FROM badania_ct WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: results.php");
        exit();

    } catch(PDOException $e) {
        die("Błąd bazy danych: " . $e->getMessage());
    }
} else {
    header("Location: results.php");
    exit();
}
?>