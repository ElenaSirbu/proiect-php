<?php
session_start();
session_destroy(); // Distruge sesiunea
header("Location: login.php"); // Redirecționăm utilizatorul către pagina de login
exit;
?>
