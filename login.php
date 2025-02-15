<?php
session_start();

// Verificăm dacă utilizatorul este deja autentificat
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirecționăm utilizatorul către pagina principală (dashboard) dacă e deja logat
    exit;
}

// CSRF Token - generăm un token pentru a preveni CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificăm CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF invalid");
    }

    // Preluăm datele din formular
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificăm dacă utilizatorul există în baza de date
    include 'db_config.php';

    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Utilizatorul există
        $user = $result->fetch_assoc();

        // Verificăm parola
        if (password_verify($password, $user['password'])) {
            // Parola corectă, setăm sesiunea
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirecționăm utilizatorul la dashboard sau la pagina principală
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Parolă incorectă!";
        }
    } else {
        $error = "Utilizatorul nu există!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare - Hipermarket</title>

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title text-center mb-4">Autentifică-te</h1>

                        <!-- Afișăm mesajul de eroare, dacă există -->
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <!-- Formular de login -->
                        <form method="POST" action="login.php">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="form-group">
                                <label for="username">Nume utilizator:</label>
                                <input type="text" class="form-control" id="username" name="username" required placeholder="Introduceti numele de utilizator">
                            </div>

                            <div class="form-group">
                                <label for="password">Parolă:</label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Introduceti parola">
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Autentifică-te</button>
                        </form>

                        <p class="text-center mt-3">Nu ai cont? <a href="create_user.php">Creează unul acum!</a></p>
                        <p class="text-center mt-3"><a href="forgot_password.php">Ai uitat parola?</a></p>
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
