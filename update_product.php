<?php
include 'db_config.php';

// Preluare ID produs
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM Products WHERE id=$id");
$product = $result->fetch_assoc();

// Preluare categorii
$categories = $conn->query("SELECT id, name FROM Categories")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category_id'];

    $stmt = $conn->prepare("UPDATE Products SET name=?, price=?, quantity=?, category_id=? WHERE id=?");
    $stmt->bind_param("sdiii", $name, $price, $quantity, $category_id, $id);

    if ($stmt->execute()) {
        echo "Produs actualizat!";
    } else {
        echo "Eroare: " . $stmt->error;
    }
}
?>

<h2>Editează produsul</h2>
<form method="POST">
    <input type="text" name="name" value="<?= $product['name'] ?>" required>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
    <input type="number" name="quantity" value="<?= $product['quantity'] ?>" required>
    <select name="category_id" required>
        <?php foreach ($categories as $category) { ?>
            <option value="<?= $category['id'] ?>" <?= ($category['id'] == $product['category_id']) ? 'selected' : '' ?>>
                <?= $category['name'] ?>
            </option>
        <?php } ?>
    </select>
    <button type="submit">Salvează</button>
</form>
