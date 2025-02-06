<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

echo "<h1>Salut, " . $_SESSION['username'] . "!</h1>";
echo "<p>Ai acces la dashboard ca " . $_SESSION['role'] . ".</p>";

?>
