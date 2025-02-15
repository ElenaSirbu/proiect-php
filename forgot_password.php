<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Verificăm dacă emailul există în baza de date
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Creăm un token pentru resetare parolă
        $token = bin2hex(random_bytes(50)); // Generăm un token aleator

        // Salvăm token-ul în baza de date
        $stmt = $conn->prepare("UPDATE Users SET reset_token = ? WHERE id = ?");
        $stmt->bind_param("si", $token, $user['id']);
        $stmt->execute();

        // Trimitem email cu link pentru resetare parolă
        $reset_link = "https://example.com/reset_password.php?token=$token"; // Schimbă cu URL-ul real
        $subject = "Resetare parolă";
        $message = "Pentru a-ți reseta parola, accesează următorul link: $reset_link";
        $headers = "From: no-reply@example.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "Un email de resetare a parolei a fost trimis!";
        } else {
            echo "Eroare la trimiterea emailului!";
        }
    } else {
        echo "Adresa de email nu există!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperare Parolă</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Recuperare Parolă</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Introdu email-ul tău" required>
        <button type="submit">Trimite link-ul pentru resetare</button>
    </form>
</body>
</html>
