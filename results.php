<?php require_once 'db_connect.php'; ?>
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
        .preview-thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>

<div class="medical-header text-center">
    <h1><i class="bi bi-table"></i> Historia Badań</h1>
    <p>Baza wczytanych struktur kostnych CT</p>
</div>

<div class="container">

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> Badanie zostało pomyślnie zapisane!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Wszystkie Badania</h4>
            <a href="index.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nowe Badanie
            </a>
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
                $stmt = $conn->query("SELECT * FROM badania_ct ORDER BY created_at DESC");
                while ($row = $stmt->fetch()) {
                    $anatomy_label = match($row['anatomy_type']) {
                        'pelvis' => 'Miednica',
                        'sternum' => 'Mostek',
                        default => ucfirst($row['anatomy_type'])
                    };

                    $preview = '';
                    if (!empty($row['preview_path']) && file_exists($row['preview_path'])) {
                        $preview = "<img src='{$row['preview_path']}' class='preview-thumb' 
                                    data-bs-toggle='tooltip' title='Kliknij aby powiększyć'
                                    onclick='window.open(\"{$row['preview_path']}\", \"_blank\")'>";
                    } else {
                        $preview = "<span class='text-muted small'>brak</span>";
                    }

                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td><strong>{$row['patient_id']}</strong></td>
                        <td>{$anatomy_label}</td>
                        <td><small class='text-muted'>{$row['file_name']}</small></td>
                        <td>{$row['notes']}</td>
                        <td>{$row['created_at']}</td>
                        <td>
                            <a href='delete.php?id={$row['id']}'
                               onclick='return confirm(\"Czy na pewno chcesz usunąć to badanie?\")'
                               class='btn btn-danger btn-sm'>✕</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>