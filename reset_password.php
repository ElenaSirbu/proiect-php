<?php
include 'db_config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificăm dacă token-ul există în baza de date
    $stmt = $conn->prepare("SELECT * FROM Users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['password'];

            // Securizăm parola
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Actualizăm parola în baza de date și ștergem token-ul
            $stmt = $conn->prepare("UPDATE Users SET password = ?, reset_token = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user['id']);
            $stmt->execute();

            echo "Parola ta a fost resetată cu succes!";
        }
    } else {
        echo "Token invalid!";
    }
} else {
    echo "Nu a fost găsit niciun token!";
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetare Parolă</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Resetare Parolă</h2>
    <form method="POST">
        <input type="password" name="password" placeholder="Noua parolă" required>
        <button type="submit">Resetează Parola</button>
    </form>
</body>
</html>
