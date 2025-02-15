<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Criptarea parolei
    $email = $_POST['email'];

    // Verifică dacă utilizatorul există deja
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "Utilizatorul există deja!";
    } else {
        // Inserare utilizator în DB
        $stmt = $conn->prepare("INSERT INTO Users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $role = 'client'; // Default role pentru utilizator nou
        $stmt->bind_param("ssss", $username, $password, $email, $role);

        if ($stmt->execute()) {
            echo "Utilizator creat cu succes!";
        } else {
            echo "Eroare la crearea utilizatorului: " . $stmt->error;
        }
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
