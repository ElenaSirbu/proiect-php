<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Preluăm comenzile utilizatorului
$stmt = $conn->prepare("SELECT * FROM Orders WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comenzile tale</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Comenzile tale</h2>
    <table>
        <tr>
            <th>ID Comandă</th>
            <th>Total</th>
            <th>Status</th>
            <th>Detalii</th>
        </tr>
        <?php while ($order = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo $order['total']; ?></td>
                <td><?php echo $order['status']; ?></td>
                <td><a href="order_details.php?order_id=<?php echo $order['id']; ?>">Vezi detalii</a></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
