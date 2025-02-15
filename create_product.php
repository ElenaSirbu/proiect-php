<?php
include 'db_config.php';
session_start();

// Verificăm dacă utilizatorul este autentificat și are rol de admin sau angajat
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'administrator' && $_SESSION['role'] !== 'angajat')) {
    // Dacă nu este admin sau angajat, îl redirecționăm la dashboard
    header("Location: dashboard.php");
    exit;
}

// Preluăm categoriile pentru dropdown
$result = $conn->query("SELECT id, name FROM Categories");
$categories = $result->fetch_all(MYSQLI_ASSOC);

// Procesăm formularul de adăugare produs
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preluăm datele din formular
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category_id'];

    // Pregătim interogarea pentru inserare
    $stmt = $conn->prepare("INSERT INTO Products (name, price, quantity, category_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdii", $name, $price, $quantity, $category_id);

    // Executăm interogarea și afișăm mesaj de succes sau eroare
    if ($stmt->execute()) {
        echo "<p class='alert alert-success'>Produs adăugat cu succes!</p>";
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
    <title>Adaugă Produs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Adaugă un produs nou</h2>

    <!-- Formularul de adăugare produs -->
    <form method="POST">
        <div class="form-group">
            <label for="name">Nume Produs</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Nume produs" required>
        </div>

        <div class="form-group">
            <label for="price">Preț</label>
            <input type="number" step="0.01" class="form-control" name="price" id="price" placeholder="Preț" required>
        </div>

        <div class="form-group">
            <label for="quantity">Cantitate</label>
            <input type="number" class="form-control" name="quantity" id="quantity" placeholder="Cantitate" required>
        </div>

        <div class="form-group">
            <label for="category_id">Categorie</label>
            <select name="category_id" class="form-control" id="category_id" required>
                <option value="">Alege categoria</option>
                <?php foreach ($categories as $category) {
                    echo "<option value='{$category['id']}'>{$category['name']}</option>";
                } ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Adaugă Produs</button>
        <a href="dashboard.php" class="btn btn-secondary">Înapoi la Dashboard</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
