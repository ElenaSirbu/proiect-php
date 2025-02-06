<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('db_config.php'); 

$username = $_SESSION['username'];

$query = "SELECT role FROM utilizatori WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Produse</title>
</head>
<body>
    <h1>Produse disponibile</h1>
    
    <?php
    if ($role == 'admin') {

        echo "<h2>Admin: Poți adăuga, edita sau șterge produse.</h2>";

        echo "<a href='adaugare_produs.php'>Adaugă produs</a> | <a href='modificare_produs.php'>Modifică produs</a> | <a href='sterge_produs.php'>Șterge produs</a>";
    } elseif ($role == 'angajat') {
        echo "<h2>Angajat: Poți gestiona produsele din magazin.</h2>";
        echo "<p>Vizualizează produsele din magazin.</p>";
    } elseif ($role == 'client') {
        echo "<h2>Client: Poți comanda produse și vizualiza detalii.</h2>";
        echo "<a href='comanda_produs.php'>Comandă produs</a>";
    }
    ?>
</body>
</html>
