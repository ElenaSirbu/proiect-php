<?php
include 'db_config.php';
session_start();

// Verificăm dacă utilizatorul este autentificat și are rol de admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header("Location: dashboard.php");
    exit;
}

// Generăm și stocăm token CSRF dacă nu există deja
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {
    // Verificare CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("<p style='color: red;'>Eroare CSRF detectată!</p>");
    }

    $category_name = $_POST['category_name']; 

    // Pregătim interogarea pentru a șterge categoria după nume
    $stmt = $conn->prepare("DELETE FROM Categories WHERE name = ?");
    $stmt->bind_param("s", $category_name); // Bind pentru string

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Categorie ștearsă cu succes!</p>";
    } else {
        echo "<p style='color: red;'>Eroare la ștergere: " . htmlspecialchars($stmt->error) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Șterge Categorie</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Șterge o categorie</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <label for="category_name">Categorie:</label>
            <select class="form-control" name="category_name" required>
                <option value="">Alege o categorie</option>
                <?php
                    // Obținem toate categoriile din baza de date
                    $query = "SELECT name FROM Categories";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nu sunt categorii disponibile</option>";
                    }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-danger">Șterge</button>
        <a href="dashboard.php" class="btn btn-secondary">Înapoi la Dashboard</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
