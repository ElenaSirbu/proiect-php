<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Interogăm comenzile utilizatorului
$query = "SELECT * FROM Orders WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comenzile mele</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Comenzile mele</h2>

    <?php if ($orders->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID Comandă</th>
                <th>Status</th>
                <th>Total</th>
                <th>Detalii</th>
            </tr>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td><?php echo $order['total']; ?> RON</td>
                    <td>
                        <a href="view_order_details.php?order_id=<?php echo $order['id']; ?>">Vezi Detalii</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Nu ai plasat nicio comandă încă.</p>
    <?php endif; ?>
</body>
</html>
