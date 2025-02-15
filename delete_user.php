<?php
session_start();

// Verificăm dacă utilizatorul este autentificat și are rolul de admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    // Dacă nu este autentificat sau nu este admin, redirecționăm la dashboard
    header("Location: dashboard.php");
    exit();
}

include 'db_config.php';

// Verificăm dacă există parametrul 'id' în URL
if (!isset($_GET['id'])) {
    die("ID invalid");
}

$user_id = $_GET['id'];

// Pregătim și executăm ștergerea utilizatorului din baza de date
$stmt = $conn->prepare("DELETE FROM Users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Dacă utilizatorul a fost șters cu succes, redirecționăm înapoi la lista de utilizatori
    echo "Utilizator șters cu succes!<br>";
    echo '<a href="list_users.php" class="btn btn-primary">Înapoi la lista utilizatorilor</a>';
} else {
    echo "Eroare la ștergerea utilizatorului: " . $stmt->error;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Șterge utilizator</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <h2>Utilizatorul a fost șters cu succes!</h2>
        <a href="list_users.php" class="btn btn-primary">Înapoi la lista utilizatorilor</a>
    </div>

    <!-- Bootstrap JS și jQuery (CDN) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
