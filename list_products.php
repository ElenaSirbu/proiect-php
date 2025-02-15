<?php
session_start();

// Verificăm dacă utilizatorul este autentificat și are rolul de angajat sau admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'angajat' && $_SESSION['role'] !== 'administrator')) {
    // Dacă nu e logat sau rolul nu e angajat sau administrator, redirecționăm la login
    header("Location: login.php");
    exit;
}

// Include conexiunea la baza de date
include 'db_config.php';

// Prevenim SQL Injection folosind prepared statements
$stmt = $conn->prepare("SELECT Products.id, Products.name, Products.price, Products.quantity, Categories.name AS category 
                        FROM Products 
                        JOIN Categories ON Products.category_id = Categories.id");
$stmt->execute();
$result = $stmt->get_result();

// Obținem produsele din baza de date
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Produse</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <h2 class="text-center mb-4">Lista Produse</h2>

        <!-- Link pentru adăugarea unui nou produs -->
        <div class="text-right mb-3">
            <a href="create_product.php" class="btn btn-success">➕ Adaugă produs</a>
        </div>

        <!-- Tabel cu produsele -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nume</th>
                    <th>Preț</th>
                    <th>Cantitate</th>
                    <th>Categorie</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) { ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?> RON</td>
                        <td><?= htmlspecialchars($product['quantity']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td>
                            <a href="update_product.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-warning btn-sm">✏️ Editează</a> 
                            | 
                            <a href="delete_product.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Sigur ștergi produsul?')">🗑️ Șterge</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Buton pentru a reveni la dashboard -->
        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-primary">Înapoi la Dashboard</a>
        </div>
    </div>

    <!-- Scripturile necesare pentru Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
