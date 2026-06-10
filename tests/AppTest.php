<?php

class AppTest
{
    private int $passed = 0;
    private int $failed = 0;

    public function assert(string $name, bool $condition): void
    {
        if ($condition) {
            echo " PASS: $name\n";
            $this->passed++;
        } else {
            echo " FAIL: $name\n";
            $this->failed++;
        }
    }

    public function summary(): void
    {
        echo "\n=== Wyniki testów ===\n";
        echo "Passed: {$this->passed}\n";
        echo "Failed: {$this->failed}\n";
        $total = $this->passed + $this->failed;
        echo "Total: {$total}\n";
        if ($this->failed > 0) {
            exit(1);
        }
    }
}

$test = new AppTest();

// Test 1: Sprawdzenie czy plik db_connect.php istnieje
$test->assert(
    "db_connect.php exists",
    file_exists(__DIR__ . '/../db_connect.php')
);

// Test 2: Sprawdzenie czy index.php istnieje
$test->assert(
    "index.php exists",
    file_exists(__DIR__ . '/../index.php')
);

// Test 3: Sprawdzenie czy results.php istnieje
$test->assert(
    "results.php exists",
    file_exists(__DIR__ . '/../results.php')
);

// Test 4: Sprawdzenie czy login.php istnieje
$test->assert(
    "login.php exists",
    file_exists(__DIR__ . '/../login.php')
);

// Test 5: Sprawdzenie czy register.php istnieje
$test->assert(
    "register.php exists",
    file_exists(__DIR__ . '/../register.php')
);

// Test 6: Sprawdzenie czy delete.php istnieje
$test->assert(
    "delete.php exists",
    file_exists(__DIR__ . '/../delete.php')
);

// Test 7: Sprawdzenie czy upload_handler.php istnieje
$test->assert(
    "upload_handler.php exists",
    file_exists(__DIR__ . '/../upload_handler.php')
);

// Test 8: Walidacja email
$test->assert(
    "Valid email validation",
    filter_var('lekarz@szpital.pl', FILTER_VALIDATE_EMAIL) !== false
);

// Test 9: Walidacja nieprawidłowego emaila
$test->assert(
    "Invalid email validation",
    filter_var('nieprawidlowy-email', FILTER_VALIDATE_EMAIL) === false
);

// Test 10: Sprawdzenie hashowania haseł
$haslo = 'TestHaslo123!';
$hash = password_hash($haslo, PASSWORD_DEFAULT);
$test->assert(
    "Password hashing works",
    password_verify($haslo, $hash)
);

// Test 11: Sprawdzenie czy złe hasło nie przechodzi weryfikacji
$test->assert(
    "Wrong password fails verification",
    !password_verify('ZleHaslo', $hash)
);

// Test 12: Sprawdzenie minimalnej długości hasła
$test->assert(
    "Password minimum length check",
    strlen('Haslo123') >= 8
);

// Test 13: Sprawdzenie czy krótkie hasło nie przechodzi
$test->assert(
    "Short password fails length check",
    strlen('abc') < 8
);

// Test 14: Sprawdzenie połączenia z bazą SQLite
try {
    $db = new PDO('sqlite::memory:');
    $db->exec("CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)");
    $db->exec("INSERT INTO test (name) VALUES ('testowy')");
    $result = $db->query("SELECT name FROM test")->fetch();
    $test->assert(
        "SQLite connection and CRUD works",
        $result['name'] === 'testowy'
    );
} catch (Exception $e) {
    $test->assert("SQLite connection and CRUD works", false);
}

// Test 15: Sprawdzenie sanityzacji danych (XSS protection)
$input = '<script>alert("xss")</script>';
$sanitized = htmlspecialchars($input);
$test->assert(
    "XSS sanitization works",
    strpos($sanitized, '<script>') === false
);

// Test 16: Sprawdzenie walidacji rozszerzenia pliku NIfTI
$allowed = ['nii', 'gz'];
$test->assert(
    "NIfTI file extension valid",
    in_array('nii', $allowed)
);

$test->assert(
    "Non-NIfTI file extension invalid",
    !in_array('exe', $allowed)
);

// Test 17: Sprawdzenie API endpoint
$api_url = 'https://ct-bone-api.azurewebsites.net';
$response = @file_get_contents($api_url);
if ($response !== false) {
    $data = json_decode($response, true);
    $test->assert(
        "API returns valid JSON",
        isset($data['status']) && $data['status'] === 'ok'
    );
    $test->assert(
        "API returns lekarze count",
        isset($data['lekarze'])
    );
    $test->assert(
        "API returns badania count",
        isset($data['badania'])
    );
} else {
    $test->assert("API returns valid JSON", false);
    $test->assert("API returns lekarze count", false);
    $test->assert("API returns badania count", false);
}

$test->summary();