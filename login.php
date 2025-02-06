<?php
session_start();

if (isset($_SESSION['utilizator'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == 'admin' && $password == 'parolaadmin') {
        $_SESSION['utilizator'] = ['username' => 'admin', 'rol' => 'admin'];
        header('Location: dashboard.php');
        exit();
    } elseif ($username == 'angajat' && $password == 'parolaangajat') {
        $_SESSION['utilizator'] = ['username' => 'angajat', 'rol' => 'angajat'];
        header('Location: dashboard.php');
        exit();
    } elseif ($username == 'client' && $password == 'parolaclient') {
        $_SESSION['utilizator'] = ['username' => 'client', 'rol' => 'client'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Username sau parolă incorecte.";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare</title>
</head>
<body>
    <h1>Autentificare</h1>

    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Parolă:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Autentifică-te</button>
    </form>
    <p>Nu ai cont? <a href="register.php">Creează un cont</a></p>
</body>
</html>
