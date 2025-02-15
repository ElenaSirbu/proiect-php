<?php
include 'db_config.php';

if (!isset($_GET['id'])) {
    die("ID invalid");
}

$user_id = $_GET['id'];

// Ștergem utilizatorul din baza de date
$stmt = $conn->prepare("DELETE FROM Users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo "Utilizator șters!";
} else {
    echo "Eroare: " . $stmt->error;
}
?>
<a href="list_users.php">Înapoi la lista utilizatorilor</a>
