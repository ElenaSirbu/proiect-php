<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include('db_config.php');

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $query = "SELECT * FROM produse WHERE id = '$product_id'";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['name'];
    $product_price = $_POST['price'];
    $product_description = $_POST['description'];

    $update_query = "UPDATE produse SET name = '$product_name', price = '$product_price', description = '$product_description' WHERE id = '$product_id'";
    mysqli_query($conn, $update_query);
    header("Location: admin_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editează Produs</title>
</head>
<body>
    <h1>Editează Produs</h1>
    <form action="edit_product.php?id=<?php echo $product['id']; ?>" method="POST">
        <label for="name">Nume produs:</label>
        <input type="text" name="name" id="name" value="<?php echo $product['name']; ?>" required><br>
        
        <label for="price">Preț:</label>
        <input type="text" name="price" id="price" value="<?php echo $product['price']; ?>" required><br>

        <label for="description">Descriere:</label>
        <textarea name="description" id="description" required><?php echo $product['description']; ?></textarea><br>

        <input type="submit" value="Actualizează produs">
    </form>
</body>
</html>
