<?php
session_start();
include 'db_config.php';

if (!isset($_GET['order_id'])) {
    echo "Comanda nu a fost găsită!";
    exit;
}

$order_id = $_GET['order_id'];

// Obținem detaliile comenzii
$query = "SELECT o.id, o.total, o.status, o.created_at, oi.product_id, oi.quantity, oi.price, p.name
          FROM Orders o
          JOIN OrderItems oi ON o.id = oi.order_id
          JOIN Products p ON oi.product_id = p.id
          WHERE o.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    echo "Detalii comanda nu disponibile!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalii Comandă</title>
</head>
<body>
    <h2>Detalii Comandă #<?php echo $order['id']; ?></h2>
    <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
    <p><strong>Total:</strong> <?php echo $order['total']; ?> RON</p>
    <p><strong>Data:</strong> <?php echo $order['created_at']; ?></p>

    <table border="1">
        <thead>
            <tr>
                <th>Produs</th>
                <th>Cantitate</th>
                <th>Preț</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Afișăm produsele din comandă
            while ($item = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
                echo "<td>" . htmlspecialchars($item['price']) . " RON</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
