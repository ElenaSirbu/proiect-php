<?php
include 'db_config.php';
session_start();

// Verificăm dacă formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificăm dacă token-ul CSRF este valid
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Eroare de securitate! Formularul a fost invalidat.");
    }

    // Preluăm și validăm datele
    $username = htmlspecialchars(trim($_POST['username']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Criptarea parolei
    $email = htmlspecialchars(trim($_POST['email']));

    // Verifică dacă utilizatorul există deja
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    
    if ($result->num_rows > 0) {
        echo "<p class='alert alert-danger'>Utilizatorul există deja!</p>";
    } else {
        // Inserare utilizator în DB
        $stmt = $conn->prepare("INSERT INTO Users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $role = 'client'; // Default role pentru utilizator nou
        $stmt->bind_param("ssss", $username, $password, $email, $role);
        $site_key = '6Le1TNgqAAAAAENie2ZNrU4CIFd6lAXPDzhBGWsK'; // Înlocuiește cu cheia ta publică

// Dacă formularul este trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verifică reCAPTCHA
    $secret_key = '6Le1TNgqAAAAACMJ_S-b0_S1AOXv6uunlD-J8R2t'; // Înlocuiește cu cheia ta secretă
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $recaptcha_response
    ];

    // Verifică dacă reCAPTCHA este valid
    $options = [
        'http' => [
            'method' => 'POST',
            'content' => http_build_query($data),
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $verify_response = file_get_contents($url, false, $context);
    $response_keys = json_decode($verify_response);

    // Dacă reCAPTCHA nu este valid
    if (intval($response_keys->success) !== 1) {
        echo "Verificarea reCAPTCHA a eșuat. Te rugăm să încerci din nou.";
    } else {
        // Continuă cu procesul de autentificare aici
        // Exemplu: Verifică datele din baza de date
    }
}
        if ($stmt->execute()) {
            echo "<p class='alert alert-success'>Utilizator creat cu succes!</p>";
        } else {
            echo "<p class='alert alert-danger'>Eroare la crearea utilizatorului: " . $stmt->error . "</p>";
        }
    }
}

// Generăm un token CSRF pentru protecție
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creare Cont - Hipermarket</title>

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title text-center mb-4">Crează un cont nou</h1>

                        <!-- Formularul de creare cont -->
                        <form method="POST" action="create_user.php">
                            <div class="form-group">
                                <label for="username">Nume utilizator:</label>
                                <input type="text" class="form-control" id="username" name="username" required placeholder="Introduceti numele de utilizator">
                            </div>

                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="Introduceti adresa de email">
                            </div>

                            <div class="form-group">
                                <label for="password">Parolă:</label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Introduceti parola">
                            </div>

                            <!-- Token CSRF -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                            <button type="submit" class="btn btn-primary btn-block">Creează cont</button>
                        </form>

                        <p class="text-center mt-3">Ai deja un cont? <a href="login.php">Autentifică-te</a></p>
                        <a href="dashboard.php" class="btn btn-secondary btn-block mt-3">Înapoi la Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS și jQuery (CDN) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
