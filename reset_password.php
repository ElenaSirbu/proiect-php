<?php
include 'db_config.php';

// Verificăm dacă token-ul este valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificăm dacă token-ul există în baza de date
    $stmt = $conn->prepare("SELECT * FROM Users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Preluăm parola din formular
            $new_password = $_POST['password'];

            // Validăm parola
            if (strlen($new_password) < 8) {
                echo "Parola trebuie să aibă cel puțin 8 caractere.";
                exit;
            }

            // Securizăm parola
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Actualizăm parola în baza de date și ștergem token-ul
            $stmt = $conn->prepare("UPDATE Users SET password = ?, reset_token = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user['id']);
            $stmt->execute();

            echo "Parola ta a fost resetată cu succes!";
        }
    } else {
        echo "Token invalid!";
    }
} else {
    echo "Nu a fost găsit niciun token!";
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetare Parolă</title>
    <!-- Include Bootstrap CSS pentru design modern -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h2 class="text-center mb-4">Resetare Parolă</h2>

                <!-- Formularul pentru resetarea parolei -->
                <form method="POST">
                    <div class="form-group">
                        <label for="password">Noua Parolă</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Introduceți noua parolă" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Resetează Parola</button>
                </form>

                <!-- Butoane pentru navigare -->
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-link">Înapoi la login</a>
                    <br>
                    <a href="forgot_password.php" class="btn btn-link">Retrimiteți linkul de resetare</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS și jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
