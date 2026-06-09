<?php
require_once 'db_connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT preview_path FROM badania_ct WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if ($row && !empty($row['preview_path']) && file_exists($row['preview_path'])) {
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