<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['lekarz_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']);
    $email = trim($_POST['email']);
    $haslo = $_POST['haslo'];
    $haslo2 = $_POST['haslo2'];

    if (empty($imie) || empty($nazwisko) || empty($email) || empty($haslo)) {
        $error = 'Wypełnij wszystkie pola.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Podaj prawidłowy adres email.';
    } elseif (strlen($haslo) < 8) {
        $error = 'Hasło musi mieć minimum 8 znaków.';
    } elseif ($haslo !== $haslo2) {
        $error = 'Hasła nie są identyczne.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM lekarze WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $error = 'Konto z tym adresem email już istnieje.';
        } else {
            $haslo_hash = password_hash($haslo, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO lekarze (imie, nazwisko, email, haslo, rola) 
                                    VALUES (:imie, :nazwisko, :email, :haslo, 'lekarz')");
            $stmt->execute([
                ':imie' => $imie,
                ':nazwisko' => $nazwisko,
                ':email' => $email,
                ':haslo' => $haslo_hash
            ]);
            $success = 'Konto zostało utworzone! Możesz się teraz zalogować.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja - CT Bone Viewer</title>
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
        <div class="col-md-6">
            <div class="card p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus logo-icon"></i>
                    <h3 class="mt-2 fw-bold">Rejestracja</h3>
                    <p class="text-muted">Utwórz konto lekarza</p>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                    <div class="mt-2">
                        <a href="login.php" class="btn btn-success btn-sm">Przejdź do logowania</a>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Imię</label>
                            <input type="text" name="imie" class="form-control" placeholder="Jan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nazwisko</label>
                            <input type="text" name="nazwisko" class="form-control" placeholder="Kowalski" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="lekarz@szpital.pl" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Hasło <small class="text-muted">(min. 8 znaków)</small></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="haslo" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Powtórz hasło</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="haslo2" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="bi bi-person-check"></i> Zarejestruj się
                    </button>
                </form>

                <hr class="my-4">
                <div class="text-center">
                    <a href="login.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Masz już konto? Zaloguj się
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>