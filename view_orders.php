<?php
// Înlocuiește acest cod cu logica ta de a prelua comenzile din baza de date.
$query = "SELECT * FROM Orders WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($order = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($order['id']) . "</td>";
    echo "<td>" . htmlspecialchars($order['total']) . " RON</td>";
    echo "<td>" . htmlspecialchars($order['status']) . "</td>";
    echo "<td><a href='view_order_details.php?order_id=" . htmlspecialchars($order['id']) . "'>Vezi detalii</a></td>";
    echo "</tr>";
}
?>
