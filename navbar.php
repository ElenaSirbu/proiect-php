<?php
session_start();

if (!isset($_SESSION['utilizator'])) {
    echo "Te rugăm să te autentifici.";
    exit();
}

$rol = $_SESSION['utilizator']['rol'];
?>

<nav>
    <ul>
        <?php if ($rol == 'admin'): ?>
            <li><a href="admin_dashboard.php">Panou Admin</a></li>
            <li><a href="administrare_utilizatori.php">Administrare Utilizatori</a></li>
            <li><a href="monitorizare_vanzari.php">Monitorizare Vânzări</a></li>
            <li><a href="rapoarte_financiare.php">Rapoarte Financiare</a></li>
        <?php elseif ($rol == 'angajat'): ?>
            <li><a href="angajat_dashboard.php">Panou Angajat</a></li>
            <li><a href="gestionare_produse.php">Gestionare Produse</a></li>
        <?php elseif ($rol == 'client'): ?>
            <li><a href="client_dashboard.php">Panou Client</a></li>
            <li><a href="cauta_produse.php">Căutare Produse</a></li>
        <?php endif; ?>

        <li><a href="logout.php">Deconectare</a></li>
    </ul>
</nav>
