<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['lekarz_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Badań - CT Bone Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .medical-header { background: linear-gradient(135deg, #1a3c6e 0%, #3c6eb4 100%); color: white; padding: 2rem 0; margin-bottom: 2rem; border-radius: 0 0 25px 25px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark" style="background-color: #1a3c6e;">
    <div class="container">
        <span class="navbar-brand"><i class="bi bi-hospital"></i> CT Bone Viewer</span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($_SESSION['lekarz_imie'] . ' ' . $_SESSION['lekarz_nazwisko']) ?>
            </span>
            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-plus-circle"></i> Nowe badanie
            </a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Wyloguj
            </a>
        </div>
    </div>
</nav>

<div class="container">

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-check-circle"></i> Badanie zostało pomyślnie zapisane!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card p-4 mt-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0"><i class="bi bi-table"></i> Moje Badania</h4>
        </div>

        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ID Pacjenta</th>
                    <th>Anatomia</th>
                    <th>Pliki</th>
                    <th>Notatki</th>
                    <th>Data</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT * FROM badania_ct WHERE lekarz_id = :lid ORDER BY created_at DESC");
                $stmt->execute([':lid' => $_SESSION['lekarz_id']]);
                $badania = $stmt->fetchAll();

                if (empty($badania)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        Brak zapisanych badań. 
                        <a href="index.php">Dodaj pierwsze badanie.</a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($badania as $row):
                    $anatomy_label = match($row['anatomy_type']) {
                        'pelvis' => 'Miednica',
                        'sternum' => 'Mostek',
                        default => ucfirst($row['anatomy_type'])
                    };
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td><strong>" . htmlspecialchars($row['patient_id']) . "</strong></td>
                        <td>{$anatomy_label}</td>
                        <td><small class='text-muted'>" . htmlspecialchars($row['file_name']) . "</small></td>
                        <td>" . htmlspecialchars($row['notes']) . "</td>
                        <td>{$row['created_at']}</td>
                        <td>
                            <a href='delete.php?id={$row['id']}'
                               onclick='return confirm(\"Czy na pewno chcesz usunąć to badanie?\")'
                               class='btn btn-danger btn-sm'>✕</a>
                        </td>
                    </tr>";
                endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>