<?php
session_start();

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Dacă nu e autentificat, îl redirecționăm la login
    exit;
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Salut, <?php echo $_SESSION['username']; ?>!</h2>
    <p>Ai acces ca: <?php echo $_SESSION['role']; ?></p>

    <!-- Aici poți adăuga link-uri către diverse funcționalități ale aplicației tale -->
    <a href="logout.php">Ieși din cont</a>
</body>
</html>
