<?php
include 'db_config.php';

// Preluare categorii pentru dropdown
$result = $conn->query("SELECT id, name FROM Categories");
$categories = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category_id'];

    $stmt = $conn->prepare("INSERT INTO Products (name, price, quantity, category_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdii", $name, $price, $quantity, $category_id);

    if ($stmt->execute()) {
        echo "Produs adăugat!";
    } else {
        echo "Eroare: " . $stmt->error;
    }
}
?>

<h2>Adaugă un produs</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Nume produs" required>
    <input type="number" step="0.01" name="price" placeholder="Preț" required>
    <input type="number" name="quantity" placeholder="Cantitate" required>
    <select name="category_id" required>
        <option value="">Alege categoria</option>
        <?php foreach ($categories as $category) {
            echo "<option value='{$category['id']}'>{$category['name']}</option>";
        } ?>
    </select>
    <button type="submit">Adaugă</button>
</form>
