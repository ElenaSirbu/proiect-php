<?php
$host = 'f8ogy1hm9ubgfv2s.chr7pe7iynqr.eu-west-1.rds.amazonaws.com'; 
$db = 'zuzaszw2bd0pm9si'; 
$user = 'rpcv91eoji2qyhfo';     
$pass = 'og6jb24bi3aen44g';     

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
} 
?>

