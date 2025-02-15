<?php
session_start();

// Verificăm dacă utilizatorul este logat și are rolul de administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    // Dacă nu este logat sau nu are rolul de administrator, îl redirecționăm la login
    header("Location: login.php");
    exit;
}

// Include conexiunea la baza de date
include 'db_config.php';

// Prevenim SQL Injection folosind prepared statements
$stmt = $conn->prepare("SELECT id, username, email, role FROM Users");
$stmt->execute();
$result = $stmt->get_result();

// Obținem utilizatorii din baza de date
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Utilizatori</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <h2 class="text-center mb-4">Lista Utilizatori</h2>

        <!-- Link pentru adăugarea unui nou utilizator -->
        <div class="text-right mb-3">
            <a href="create_user.php" class="btn btn-success">➕ Adaugă Utilizator</a>
        </div>

        <!-- Tabel cu utilizatorii -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <a href="update_user.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-warning btn-sm">✏️ Editează</a> 
                            | 
                            <a href="delete_user.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Sigur ștergi utilizatorul?')">🗑️ Șterge</a>
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
