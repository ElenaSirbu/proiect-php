<?php
include 'db_config.php';

$result = $conn->query("SELECT * FROM Categories");
echo "<table border='1'><tr><th>ID</th><th>Nume</th><th>Acțiuni</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td>
    <td><a href='categories_update.php?id={$row['id']}'>Edit</a> | 
    <a href='categories_delete.php?id={$row['id']}'>Șterge</a></td></tr>";
}
echo "</table>";
?>
