<?php
require_once 'db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['nifti_files'])) {
    $patient_id = $_POST['patient_id'];
    $anatomy = $_POST['anatomy'];
    $notes = $_POST['notes'] ?? '';

    $upload_dir = 'uploads/';
    $preview_dir = 'previews/';

    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
    if (!is_dir($preview_dir)) { mkdir($preview_dir, 0777, true); }

    $timestamp = time();
    $uploaded_paths = [];
    $original_names = [];

    foreach ($_FILES['nifti_files']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['nifti_files']['error'][$key] === UPLOAD_ERR_OK) {
            $name = basename($_FILES['nifti_files']['name'][$key]);
            $unique_name = $timestamp . "_" . $name;
            $target_path = $upload_dir . $unique_name;
            if (move_uploaded_file($tmp_name, $target_path)) {
                $uploaded_paths[] = $target_path;
                $original_names[] = $name;
            }
        }
    }

    if (empty($uploaded_paths)) {
        die("Błąd: Nie udało się wgrać żadnego pliku.");
    }

    $all_filenames = implode(", ", $original_names);
    $preview_name = $timestamp . "_preview.png";
    $preview_path = $preview_dir . $preview_name;

    if (isset($_POST['snapshot']) && !empty($_POST['snapshot'])) {
        $img_data = $_POST['snapshot'];
        $img_data = str_replace('data:image/png;base64,', '', $img_data);
        $img_data = str_replace(' ', '+', $img_data);
        file_put_contents($preview_path, base64_decode($img_data));
    }

    try {
        $sql = "INSERT INTO badania_ct (patient_id, anatomy_type, file_name, notes, preview_path)
                VALUES (:pid, :anat, :fname, :notes, :prev)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':pid'   => $patient_id,
            ':anat'  => $anatomy,
            ':fname' => $all_filenames,
            ':notes' => $notes,
            ':prev'  => $preview_path
        ]);

        header("Location: results.php?success=1");
        exit();

    } catch(PDOException $e) {
        echo "Błąd bazy danych: " . $e->getMessage();
    }
} else {
    echo "Nieprawidłowe żądanie.";
}
?>