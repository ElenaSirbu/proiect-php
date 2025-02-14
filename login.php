<?php
session_start();

// Verifică dacă utilizatorul este deja logat
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Conectare la baza de date
$host = 'f8ogy1hm9ubgfv2s.chr7pe7iynqr.eu-west-1.rds.amazonaws.com'; 
$db = 'zuzaszw2bd0pm9si'; 
$user = 'rpcv91eoji2qyhfo';     
$pass = 'og6jb24bi3aen44g';  

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexiune eșuată: " . $e->getMessage());
}

// Procesare formular de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $parola = $_POST['parola'];

    // Verifică credențialele
    $sql = "SELECT id, username, role FROM Users WHERE email = :email AND password = SHA2(:parola, 256)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':parola', $parola);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Email sau parolă incorectă!";
    }
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
    <h1>Login</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="parola">Parolă:</label>
        <input type="password" id="parola" name="parola" required>
        <br>
        <button type="submit">Login</button>
    </form>
    <p>Nu ai cont? <a href="register.php">Înregistrează-te aici</a>.</p>
</body>
</html>