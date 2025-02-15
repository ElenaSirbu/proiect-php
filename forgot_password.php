<?php
session_start(); // Începe sesiunea pentru a gestiona utilizatorul

include 'db_config.php'; // Conectare la baza de date

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Verifică dacă formularul este trimis prin POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validăm și curățăm emailul utilizatorului
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Verificăm dacă emailul este valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresa de email nu este validă!";
        exit();
    }

    // Protecție CSRF: Verificăm dacă token-ul CSRF este valid
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Tokenul CSRF este invalid.");
    }

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

        // Crează un obiect PHPMailer
        $mail = new PHPMailer();

        // Setează că folosim SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Folosește serverul SMTP dorit (de exemplu, Gmail)
        $mail->SMTPAuth = true;
        $mail->Username = 'adresa_ta_de_email@gmail.com';  // Înlocuiește cu adresa ta de email
        $mail->Password = 'parola_ta_de_email';  // Înlocuiește cu parola ta de email
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;  // Portul SMTP pentru Gmail

        // Setează expeditorul și destinatarul
        $mail->setFrom('no-reply@example.com', 'Numele Tău');
        $mail->addAddress($email, $user['username']);  // Adresa destinatarului

        // Setează subiectul și corpul mesajului
        $reset_link = "https://example.com/reset_password.php?token=$token"; // URL-ul real trebuie actualizat
        $mail->Subject = 'Resetare parolă';
        $mail->Body    = "Pentru a-ți reseta parola, accesează următorul link: $reset_link";

        // Trimite email-ul
        if ($mail->send()) {
            echo "Un email de resetare a parolei a fost trimis! Verifică-ți inboxul.";
        } else {
            echo "Eroare la trimiterea emailului. Te rugăm să încerci din nou!";
        }
    } else {
        echo "Adresa de email nu există!";
    }
}

// Generăm un token CSRF pentru formular
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperare Parolă</title>
    <!-- Include Bootstrap CSS din CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Recuperare Parolă</h2>
                        
                        <!-- Formularul de resetare a parolei -->
                        <form method="POST">
                            <!-- Token CSRF pentru protecție -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="form-group">
                                <label for="email">Introdu email-ul tău</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Introdu email-ul" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Trimite link-ul pentru resetare</button>
                        </form>
                        
                        <!-- Butonul de întoarcere la pagina de login -->
                        <p class="text-center mt-3">Ai deja un cont? <a href="login.php">Autentifică-te aici</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS și jQuery din CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
