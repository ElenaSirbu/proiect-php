<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO Users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hashed_password, $email, $role);

    if ($stmt->execute()) {
        echo "Utilizator adăugat cu succes!";
    } else {
        echo "Eroare: " . $stmt->error;
    }
}
?>

<h2>Adaugă Utilizator</h2>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Parolă" required>
    <input type="email" name="email" placeholder="Email" required>
    <select name="role">
        <option value="client">Client</option>
        <option value="angajat">Angajat</option>
        <option value="administrator">Administrator</option>
    </select>
    <button type="submit">Adaugă Utilizator</button>
</form>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Include fișierul CSS -->
    <link rel="stylesheet" href="style.css"> <!-- Calea corectă în funcție de locul unde ai salvat CSS-ul -->
</head>
<body>
