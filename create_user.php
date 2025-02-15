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


<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creare Cont - Hipermarket</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h2>Crează un cont nou</h2>

        <form method="POST" action="create_user.php">
            <label for="username">Nume utilizator:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Parolă:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit" class="button">Creează cont</button>
        </form>
        <p>Ai deja un cont? <a href="login.php">Autentifică-te</a></p>
    </div>

</body>
</html>