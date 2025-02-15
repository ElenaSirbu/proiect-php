<?php
include 'db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM Categories WHERE id=$id");
    $category = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $stmt = $conn->prepare("UPDATE Categories SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);
    if ($stmt->execute()) {
        echo "Categorie actualizată!";
    }
}
?>
<form method="POST">
    <input type="text" name="name" value="<?= $category['name'] ?>" required>
    <button type="submit">Salvează</button>
</form>
