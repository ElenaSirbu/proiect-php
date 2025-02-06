<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include('db_config.php'); 


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['name'];
    $product_price = $_POST['price'];
    $product_description = $_POST['description'];

    $query = "INSERT INTO produse (name, price, description) VALUES ('$product_name', '$product_price', '$product_description')";
    mysqli_query($conn, $query);
}


$query = "SELECT * FROM produse";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Administrare Produse</title>
</head>
<body>
    <h1>Administrare Produse</h1>
    <form action="admin_products.php" method="POST">
        <label for="name">Nume produs:</label>
        <input type="text" name="name" id="name" required><br>
        
        <label for="price">Preț:</label>
        <input type="text" name="price" id="price" required><br>

        <label for="description">Descriere:</label>
        <textarea name="description" id="description" required></textarea><br>

        <input type="submit" name="add_product" value="Adaugă produs">
    </form>

    <h2>Produse existente:</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Preț</th>
                <th>Descriere</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><a href="edit_product.php?id=<?php echo $row['id']; ?>">Editează</a> | <a href="delete_product.php?id=<?php echo $row['id']; ?>">Șterge</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
