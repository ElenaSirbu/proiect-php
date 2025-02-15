<?php
$cleardb_url = parse_url(getenv("JAWSDB_URL"));

$host = $cleardb_url["host"];
$user = $cleardb_url["user"];
$pass = $cleardb_url["pass"];
$db = substr($cleardb_url["path"], 1);

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
?>
