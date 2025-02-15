<?php
session_start();

// Verificăm dacă utilizatorul este autentificat și are rolul corespunzător
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'angajat')) {
    // Dacă nu este autentificat sau nu are permisiunea, redirecționează-l către dashboard
    header("Location: dashboard.php");
    exit();
}

include 'db_config.php';

// Verificăm dacă există parametrul 'id' în URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Pregătim și executăm ștergerea produsului din baza de date
    $stmt = $conn->prepare("DELETE FROM Products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Redirecționăm utilizatorul înapoi la lista de produse
        header("Location: list_products.php");
        exit();
    } else {
        echo "Eroare la ștergerea produsului.";
    }
} else {
    // Dacă nu este specificat un ID, redirecționăm utilizatorul
    header("Location: list_products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ștergere produs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <h2>Produsul a fost șters cu succes!</h2>

        <a href="dashboard.php" class="btn btn-primary">Întoarcere la Dashboard</a>
    </div>

    <!-- Bootstrap JS și jQuery (CDN) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
