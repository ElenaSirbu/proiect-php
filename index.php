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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container text-center mt-5">
        <h1 class="display-4">Bun venit la Hipermarket!</h1>
        <p class="lead">Vă oferim cele mai bune produse la prețuri excelente.</p>

        <!-- Butoane pentru login și creare cont -->
        <div class="d-flex justify-content-center mt-4">
            <a href="login.php" class="btn btn-primary btn-lg mr-3">Autentifică-te</a>
            <a href="create_user.php" class="btn btn-success btn-lg">Creează un cont</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

