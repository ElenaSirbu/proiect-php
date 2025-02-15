<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    echo "ID-ul comenzii nu a fost furnizat!";
    exit;
}

$order_id = $_GET['order_id'];

// Preluăm detaliile comenzii din Orders
$query = "SELECT * FROM Orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Comanda nu există sau nu îți aparține!";
    exit;
}

$order = $result->fetch_assoc();

// Preluăm produsele din OrderItems
$query = "SELECT oi.*, p.name FROM OrderItems oi
          JOIN Products p ON oi.product_id = p.id
          WHERE oi.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalii Comandă</title>
</head>
<body>
    <h2>Detalii Comandă</h2>

    <h3>Comanda ID: <?php echo htmlspecialchars($order['id']); ?></h3>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
    <p><strong>Total:</strong> <?php echo htmlspecialchars($order['total']); ?> RON</p>

    <h3>Produse comandate:</h3>
    <table>
        <tr>
            <th>Produs</th>
            <th>Cantitate</th>
            <th>Preț</th>
        </tr>
        <?php while ($item = $order_items->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
            <td><?php echo htmlspecialchars($item['price']); ?> RON</td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="view-orders.php">Înapoi la comenzi</a>
</body>
</html>
