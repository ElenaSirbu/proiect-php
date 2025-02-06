<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include('db_config.php');

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $delete_query = "DELETE FROM produse WHERE id = '$product_id'";
    mysqli_query($conn, $delete_query);
}

header("Location: admin_products.php");
exit();
?>
