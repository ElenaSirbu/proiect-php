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
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Bun venit la Hipermarket!</h1>

        <p>Vă oferim cele mai bune produse la prețuri excelente.</p>

        <div class="buttons-container">
            <a href="login.php">
                <button class="button">Autentifică-te</button>
            </a>
            <a href="register.php">
                <button class="button">Creează un cont</button>
            </a>
        </div>
    </div>

</body>
</html>
