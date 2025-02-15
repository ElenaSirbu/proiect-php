<?php
include 'db_config.php';

if ($conn->connect_error) {
    die("Eroare conexiune: " . $conn->connect_error);
} else {
    echo "Conexiune reușită!";
}
?>
