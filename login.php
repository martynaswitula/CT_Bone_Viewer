<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['lekarz_id'])) {
    if ($_SESSION['lekarz_rola'] === 'admin') {
        header("Location: panel.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $haslo = $_POST['haslo'];

    if (empty($email) || empty($haslo)) {
        $error = 'Wypełnij wszystkie pola.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM lekarze WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $lekarz = $stmt->fetch();

        if ($lekarz && password_verify($haslo, $lekarz['haslo'])) {
            $_SESSION['lekarz_id'] = $lekarz['id'];
            $_SESSION['lekarz_imie'] = $lekarz['imie'];
            $_SESSION['lekarz_nazwisko'] = $lekarz['nazwisko'];
            $_SESSION['lekarz_rola'] = $lekarz['rola'];

            if ($lekarz['rola'] == 'admin') {
                header("Location: panel.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = 'Nieprawidłowy email lub hasło.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - CT Bone Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #1a3c6e 0%, #3c6eb4 100%); min-height: 100vh; display: flex; align-items: center; }
        .card { border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .logo-icon { font-size: 3rem; color: #1a3c6e; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-hospital logo-icon"></i>
                    <h3 class="mt-2 fw-bold">CT Bone Viewer</h3>
                    <p class="text-muted">Panel logowania dla lekarzy</p>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="lekarz@szpital.pl" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Hasło</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="haslo" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="bi bi-box-arrow-in-right"></i> Zaloguj się
                    </button>
                </form>

                <hr class="my-4">
                <div class="text-center">
                    <p class="text-muted mb-2">Nie masz konta?</p>
                    <a href="register.php" class="btn btn-outline-primary w-100">
                        <i class="bi bi-person-plus"></i> Zarejestruj się
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>