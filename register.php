<?php
include('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilizatori (username, password, email) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $username, $hashed_password, $email);

        if ($stmt->execute()) {
            echo "Înregistrare reușită!";
        } else {
            echo "Eroare la înregistrare: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Eroare la pregătirea interogării: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare Utilizator</title>
</head>
<body>
    <h2>Înregistrare Utilizator</h2>
    <form action="register.php" method="POST">
        <label for="username">Nume utilizator:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Parolă:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Înregistrează-te">
    </form>
</body>
</html>
