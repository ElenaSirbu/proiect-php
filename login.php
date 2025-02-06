<?php
session_start();  

include('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM utilizatori WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; 

                header("Location: dashboard.php");
                exit();
            } else {
                echo "Parola incorectă!";
            }
        } else {
            echo "Utilizatorul nu există!";
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
    <title>Login</title>
</head>
<body>
    <h2>Autentificare</h2>
    <form action="login.php" method="POST">
        <label for="username">Nume utilizator:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Parolă:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Autentificare">
    </form>
</body>
</html>
