<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obținem comenzile utilizatorului sau toate comenzile dacă este angajat/administrator
if ($_SESSION['role'] === 'administrator' || $_SESSION['role'] === 'angajat') {
    $query = "SELECT * FROM Orders";
} else {
    $query = "SELECT * FROM Orders WHERE user_id = ?";
}

$stmt = $conn->prepare($query);
if ($_SESSION['role'] !== 'administrator' && $_SESSION['role'] !== 'angajat') {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizează comenzile</title>
</head>
<body>
    <h2>Comenzile tale</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Total</th>
            <th>Status</th>
            <th>Acțiune</th>
        </tr>
        <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo $order['total']; ?> RON</td>
                <td><?php echo $order['status']; ?></td>
                <td>
                    <?php if ($order['status'] == 'plasată' && ($_SESSION['role'] == 'angajat' || $_SESSION['role'] == 'administrator')): ?>
                        <a href="process_order.php?order_id=<?php echo $order['id']; ?>&status=procesată">Procesează</a>
                    <?php endif; ?>
                    <?php if ($order['status'] == 'procesată' && ($_SESSION['role'] == 'angajat' || $_SESSION['role'] == 'administrator')): ?>
                        <a href="process_order.php?order_id=<?php echo $order['id']; ?>&status=livrată">Livrare</a>
                    <?php endif; ?>
                    <?php if ($order['status'] != 'livrată' && $_SESSION['role'] == 'client'): ?>
                        <a href="cancel_order.php?order_id=<?php echo $order['id']; ?>">Anulează comanda</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
