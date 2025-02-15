<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Verificăm dacă comanda aparține utilizatorului curent
    $query = "SELECT * FROM Orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Comanda nu există sau nu îți aparține!";
        exit;
    }

    // Detalii comanda
    $order = $result->fetch_assoc();

    // Obținem produsele din comanda
    $query = "SELECT oi.product_id, p.name, oi.quantity, oi.price FROM OrderItems oi
              JOIN Products p ON oi.product_id = p.id
              WHERE oi.order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();
} else {
    echo "ID-ul comenzii nu este valid!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalii comandă</title>
</head>
<body>
    <h2>Detalii Comandă #<?php echo htmlspecialchars($order['id']); ?></h2>

    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
    <p><strong>Total:</strong> <?php echo htmlspecialchars($order['total']); ?> RON</p>
    <p><strong>Data comenzii:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>

    <h3>Produse incluse în comandă:</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Produs</th>
                <th>Cantitate</th>
                <th>Preț unitar</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?> RON</td>
                    <td><?php echo htmlspecialchars($item['quantity'] * $item['price']); ?> RON</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <a href="view_orders.php">Înapoi la comenzi</a>
</body>
</html>
