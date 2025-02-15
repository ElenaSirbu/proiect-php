<?php
include 'db_config.php';

if (!isset($_GET['id'])) {
    die("ID invalid");
}

$user_id = $_GET['id'];

// Preluăm datele utilizatorului
$stmt = $conn->prepare("SELECT username, email, role FROM Users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE Users SET username = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $role, $user_id);

    if ($stmt->execute()) {
        echo "Utilizator actualizat!";
    } else {
        echo "Eroare: " . $stmt->error;
    }
}
?>

<h2>Editează Utilizator</h2>
<form method="POST">
    <input type="text" name="username" value="<?= $user['username'] ?>" required>
    <input type="email" name="email" value="<?= $user['email'] ?>" required>
    <select name="role">
        <option value="client" <?= $user['role'] == 'client' ? 'selected' : '' ?>>Client</option>
        <option value="angajat" <?= $user['role'] == 'angajat' ? 'selected' : '' ?>>Angajat</option>
        <option value="administrator" <?= $user['role'] == 'administrator' ? 'selected' : '' ?>>Administrator</option>
    </select>
    <button type="submit">Actualizează</button>
</form>
