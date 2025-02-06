<?php
session_start();

if (isset($_SESSION['utilizator'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principală - Hipermarket</title>
</head>
<body>
    <h1>Bun venit la Hipermarket!</h1>

    <p><a href="login.php">Autentifică-te</a></p>
    <p>Nu ai cont? <a href="register.php">Creează un cont</a></p>
</body>
</html>
