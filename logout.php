<?php
session_start();
session_unset(); // Eliberăm variabilele de sesiune
session_destroy(); // Distrugem sesiunea
header("Location: index.php"); // Redirecționăm utilizatorul pe pagina principală
exit;
?>
