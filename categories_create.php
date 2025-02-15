<?php 
include 'db_config.php';
session_start();

// Verificăm dacă utilizatorul este autentificat și are rol de admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header("Location: dashboard.php");
    exit;
}

// Generăm și stocăm token CSRF în sesiune (dacă nu există deja)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificare CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("<p style='color: red;'>Eroare CSRF detectată!</p>");
    }

    $name = trim($_POST['name']);

    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO Categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Categorie adăugată!</p>";
        } else {
            echo "<p style='color: red;'>Eroare: " . htmlspecialchars($stmt->error) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Numele categoriei nu poate fi gol!</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Categorie</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Adaugă o categorie nouă</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <input type="text" class="form-control" name="name" placeholder="Categorie nouă" required>
        </div>
        <button type="submit" class="btn btn-primary">Adaugă</button>
        <a href="dashboard.php" class="btn btn-secondary">Înapoi la Dashboard</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>



