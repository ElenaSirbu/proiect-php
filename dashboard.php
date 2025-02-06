<?php
session_start();

if (!isset($_SESSION['utilizator'])) {
    header('Location: login.php');
    exit();
}

include('navbar.php');
?>

<h1>Panou utilizator: <?php echo $_SESSION['utilizator']['username']; ?></h1>

<p>Acesta este panoul principal. În funcție de rol, vei avea acces la diferite secțiuni ale aplicației.</p>
