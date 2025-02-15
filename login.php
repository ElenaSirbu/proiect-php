<?php
session_start();

// Verificăm dacă utilizatorul este deja autentificat
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirecționăm utilizatorul către pagina principală (dashboard) dacă e deja logat
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Autentificare</h2>

    <?php
    if (isset($error)) {
        echo "<p style='color: red;'>$error</p>";
    }
    ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Autentifică-te</button>
    </form>
</body>
</html>
