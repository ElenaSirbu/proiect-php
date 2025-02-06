<?php
session_start();

include('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Previne SQL Injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "SELECT * FROM utilizatori WHERE username = '$username' AND parola = '$password'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['utilizator'] = $user;

        if ($user['rol'] == 'admin') {
            header('Location: admin_dashboard.php');  
        } elseif ($user['rol'] == 'angajat') {
            header('Location: angajat_dashboard.php');  
        } elseif ($user['rol'] == 'client') {
            header('Location: client_dashboard.php');  
        } else {
            echo "Rol necunoscut!";
        }
    } else {
        echo "Utilizator sau parolă incorecte!";
    }
}
?>
