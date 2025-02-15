<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat și dacă este angajat sau administrator
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'angajat' && $_SESSION['role'] != 'administrator')) {
    header("Location: login.php");
    exit;
}

// Preluăm toate comenzile
$query = "SELECT o.id, o.total, o.status, u.username FROM Orders o 
          JOIN Users u ON o.user_id = u.id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesare Comenzi</title>
</head>
<body>
    <h2>Comenzi de procesat</h2>

    <table border="1">
        <thead>
            <tr>
                <th>ID Comandă</th>
                <th>Utilizator</th>
                <th>Total</th>
                <th>Status</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                    <td><?php echo htmlspecialchars($order['total']); ?> RON</td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td>
                        <!-- Formular pentru schimbarea statusului comenzii -->
                        <a href="process_order.php?order_id=<?php echo htmlspecialchars($order['id']); ?>&status=procesată">Procesează</a> | 
                        <a href="process_order.php?order_id=<?php echo htmlspecialchars($order['id']); ?>&status=livrată">Livrată</a> | 
                        <a href="process_order.php?order_id=<?php echo htmlspecialchars($order['id']); ?>&status=anulată">Anulează</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
