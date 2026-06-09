<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['lekarz_id']) || $_SESSION['lekarz_rola'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_haslo'])) {
    $id = (int)$_POST['lekarz_id'];
    $nowe_haslo = $_POST['nowe_haslo'];
    if (strlen($nowe_haslo) < 8) {
        $error = 'Hasło musi mieć minimum 8 znaków.';
    } else {
        $hash = password_hash($nowe_haslo, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE lekarze SET haslo = :haslo WHERE id = :id");
        $stmt->execute([':haslo' => $hash, ':id' => $id]);
        $success = 'Hasło zostało zmienione.';
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['lekarz_id']) {
        $conn->prepare("DELETE FROM badania_ct WHERE lekarz_id = :id")->execute([':id' => $id]);
        $conn->prepare("DELETE FROM lekarze WHERE id = :id AND rola != 'admin'")->execute([':id' => $id]);
        $success = 'Konto lekarza zostało usunięte.';
    } else {
        $error = 'Nie możesz usunąć własnego konta.';
    }
}

$lekarze = $conn->query("SELECT l.*, COUNT(b.id) as liczba_badan 
                          FROM lekarze l 
                          LEFT JOIN badania_ct b ON l.id = b.lekarz_id 
                          GROUP BY l.id 
                          ORDER BY l.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admina - CT Bone Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .admin-header { background: linear-gradient(135deg, #6e1a1a 0%, #b43c3c 100%); color: white; padding: 1.5rem 0; margin-bottom: 2rem; border-radius: 0 0 25px 25px; }
    </style>
</head>
<body>

<div class="admin-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h2 class="m-0"><i class="bi bi-shield-lock"></i> Panel Administratora</h2>
            <small>Zarządzanie kontami lekarzy</small>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-hospital"></i> Aplikacja
            </a>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Wyloguj
            </a>
        </div>
    </div>
</div>

<div class="container">

    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Statystyki -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <i class="bi bi-people fs-1 text-primary"></i>
                <h3 class="mt-2"><?= count($lekarze) ?></h3>
                <p class="text-muted m-0">Wszyscy użytkownicy</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <i class="bi bi-person-check fs-1 text-success"></i>
                <h3 class="mt-2"><?= count(array_filter($lekarze, fn($l) => $l['rola'] == 'lekarz')) ?></h3>
                <p class="text-muted m-0">Lekarze</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <i class="bi bi-file-earmark-medical fs-1 text-info"></i>
                <h3 class="mt-2"><?= array_sum(array_column($lekarze, 'liczba_badan')) ?></h3>
                <p class="text-muted m-0">Wszystkich badań</p>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <h4 class="mb-3"><i class="bi bi-people"></i> Lista użytkowników</h4>

        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Imię i nazwisko</th>
                    <th>Email</th>
                    <th>Rola</th>
                    <th>Badania</th>
                    <th>Data rejestracji</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lekarze as $l): ?>
                <tr>
                    <td><?= $l['id'] ?></td>
                    <td><strong><?= htmlspecialchars($l['imie'] . ' ' . $l['nazwisko']) ?></strong></td>
                    <td><?= htmlspecialchars($l['email']) ?></td>
                    <td>
                        <?php if ($l['rola'] == 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-primary">Lekarz</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-info"><?= $l['liczba_badan'] ?></span></td>
                    <td><?= $l['created_at'] ?></td>
                    <td>
                        <?php if ($l['rola'] != 'admin'): ?>
                        <div class="d-flex gap-1">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#resetModal<?= $l['id'] ?>">
                                <i class="bi bi-key"></i> Reset hasła
                            </button>
                            <a href='admin.php?delete=<?= $l['id'] ?>'
                               onclick='return confirm("Czy na pewno chcesz usunąć konto <?= htmlspecialchars($l['imie'] . ' ' . $l['nazwisko']) ?> wraz ze wszystkimi badaniami?")'
                               class='btn btn-danger btn-sm'>
                               <i class="bi bi-trash"></i> Usuń
                            </a>
                        </div>

                        <div class="modal fade" id="resetModal<?= $l['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="bi bi-key"></i> Reset hasła — <?= htmlspecialchars($l['imie'] . ' ' . $l['nazwisko']) ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="lekarz_id" value="<?= $l['id'] ?>">
                                            <label class="form-label fw-bold">Nowe hasło</label>
                                            <input type="password" name="nowe_haslo" class="form-control"
                                                   placeholder="min. 8 znaków" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                                            <button type="submit" name="reset_haslo" class="btn btn-warning">
                                                <i class="bi bi-key"></i> Zmień hasło
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                            <span class="text-muted small">Konto chronione</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>