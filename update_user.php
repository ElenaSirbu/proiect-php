<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrator') {
    header("Location: login.php");
    exit;
}

// Verificăm dacă ID-ul este valid
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    echo "ID invalid!";
    exit;
}

// Preluăm datele utilizatorului
$stmt = $conn->prepare("SELECT username, email, role FROM Users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Utilizatorul nu a fost găsit!";
    exit;
}

$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preluăm datele din formular și validăm
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (!empty($username) && !empty($email) && in_array($role, ['client', 'angajat', 'administrator'])) {
        $stmt = $conn->prepare("UPDATE Users SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $email, $role, $user_id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Utilizator actualizat cu succes!</div>";
        } else {
            echo "<div class='alert alert-danger'>Eroare: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Toate câmpurile sunt obligatorii și rolul trebuie să fie valid!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editează Utilizator</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Editează Utilizator</h2>

        <form method="POST">
            <div class="form-group">
                <label for="username">Nume utilizator</label>
                <input type="text" class="form-control" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">Rol</label>
                <select class="form-control" name="role" id="role" required>
                    <option value="client" <?= ($user['role'] == 'client') ? 'selected' : '' ?>>Client</option>
                    <option value="angajat" <?= ($user['role'] == 'angajat') ? 'selected' : '' ?>>Angajat</option>
                    <option value="administrator" <?= ($user['role'] == 'administrator') ? 'selected' : '' ?>>Administrator</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Actualizează</button>
        </form>

        <div class="mt-3">
            <a href="list_users.php" class="btn btn-secondary">Înapoi la lista de utilizatori</a>
        </div>
    </div>

    <!-- Include Bootstrap JS și jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
