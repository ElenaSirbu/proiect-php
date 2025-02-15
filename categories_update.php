<?php
include 'db_config.php';
session_start();

// Verificăm dacă utilizatorul este autentificat și are rol de admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Protecția împotriva CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verificăm dacă a fost trimis un ID valid în GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Utilizăm prepared statement pentru a preveni SQL injection
    $stmt = $conn->prepare("SELECT * FROM Categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    // Verificăm tokenul CSRF pentru a preveni atacurile de tip CSRF
    $name = $_POST['name'];

    // Protejăm împotriva SQL Injection prin binding
    $stmt = $conn->prepare("UPDATE Categories SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);

    if ($stmt->execute()) {
        echo "<p class='alert alert-success'>Categorie actualizată cu succes!</p>";
    } else {
        echo "<p class='alert alert-danger'>Eroare: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizează Categorie</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Actualizează Categorie</h2>
    
    <!-- Formularul de actualizare a categoriei -->
    <form method="POST">
        <!-- Protecția CSRF -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <label for="name">Nume Categorie</label>
            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($category['name']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvează</button>
        <a href="dashboard.php" class="btn btn-secondary">Înapoi la Dashboard</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
