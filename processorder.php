<?php
require_once 'db_config.php';

$sql = "SELECT * FROM produse";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo $row['nume'] . " - " . $row['pret'] . " lei<br>";
}
?>