<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    $stmt = $conn->prepare("INSERT INTO Categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo "Categorie adăugată!";
    } else {
        echo "Eroare: " . $stmt->error;
    }
}
?>
<form method="POST">
    <input type="text" name="name" placeholder="Categorie nouă" required>
    <button type="submit">Adaugă</button>
</form>
