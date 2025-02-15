<?php
include 'db_config.php';

$id = $_GET['id'];
$conn->query("DELETE FROM Products WHERE id=$id");

header("Location: list_products.php");
exit();
?>
