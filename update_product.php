<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este angajat
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'angajat') {
    header("Location: login.php");
    exit;
}

// Preluare ID produs și securizare
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificăm dacă ID-ul este valid
if ($id <= 0) {
    echo "ID invalid!";
    exit;
}

// Preluare produs
$result = $conn->query("SELECT * FROM Products WHERE id=$id");
$product = $result->fetch_assoc();

// Preluare categorii
$categories = $conn->query("SELECT id, name FROM Categories")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preluăm datele din formular și validăm
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $category_id = intval($_POST['category_id']);

    if ($name && $price > 0 && $quantity >= 0 && $category_id > 0) {
        $stmt = $conn->prepare("UPDATE Products SET name=?, price=?, quantity=?, category_id=? WHERE id=?");
        $stmt->bind_param("sdiii", $name, $price, $quantity, $category_id, $id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Produs actualizat cu succes!</div>";
        } else {
            echo "<div class='alert alert-danger'>Eroare: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Toate câmpurile sunt obligatorii și trebuie să fie valide!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editează Produs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Editează Produsul</h2>

        <form method="POST">
            <div class="form-group">
                <label for="name">Numele produsului</label>
                <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="price">Prețul</label>
                <input type="number" class="form-control" name="price" id="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="quantity">Cantitate</label>
                <input type="number" class="form-control" name="quantity" id="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Categorie</label>
                <select class="form-control" name="category_id" id="category_id" required>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?= $category['id'] ?>" <?= ($category['id'] == $product['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Salvează</button>
        </form>

        <div class="mt-3">
            <a href="products_list.php" class="btn btn-secondary">Înapoi la lista de produse</a>
        </div>
    </div>

    <!-- Include Bootstrap JS și jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
