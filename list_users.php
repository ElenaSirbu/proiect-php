<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header("Location: login.php");
    exit;
}
// Verificăm dacă utilizatorul este autentificat


// Acum include conexiunea la baza de date
include 'db_config.php';

$result = $conn->query("SELECT id, username, email, role FROM Users");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<h2>Lista Utilizatori</h2>
<a href="create_user.php">➕ Adaugă Utilizator</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Acțiuni</th>
    </tr>
    <?php foreach ($users as $user) { ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['username'] ?></td>
            <td><?= $user['email'] ?></td>
            <td><?= $user['role'] ?></td>
            <td>
                <a href="update_user.php?id=<?= $user['id'] ?>">✏️ Editează</a> | 
                <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Sigur ștergi utilizatorul?')">🗑️ Șterge</a>
            </td>
        </tr>
    <?php } ?>
</table>
