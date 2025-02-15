<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verificăm dacă există un ID de comandă în URL
if (!isset($_GET['order_id'])) {
    echo "ID-ul comenzii nu este valid.";
    exit;
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Obținem detaliile comenzii
$query = "SELECT * FROM Orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    echo "Comanda nu a fost găsită.";
    exit;
}

$order = $order_result->fetch_assoc();

// Obținem produsele din comanda
$query = "SELECT oi.*, p.name, p.price FROM OrderItems oi 
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
    <title>Detalii comandă</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Detalii comandă #<?php echo $order['id']; ?></h2>
    <p>Status: <?php echo $order['status']; ?></p>
    <p>Total: <?php echo $order['total']; ?> RON</p>

    <h3>Produse comandate</h3>
    <table>
        <tr>
            <th>Produs</th>
            <th>Cantitate</th>
            <th>Preț</th>
            <th>Total</th>
        </tr>
        <?php while ($item = $order_items->fetch_assoc()): ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo $item['price']; ?> RON</td>
                <td><?php echo $item['quantity'] * $item['price']; ?> RON</td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
