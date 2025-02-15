<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obținem comenzile utilizatorului
$query = "SELECT id, total, status, created_at FROM Orders WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<?php
// Export CSV
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'angajat' && $_SESSION['role'] != 'administrator')) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['export_csv'])) {
    // Selectăm comenzile și detaliile acestora
    $query = "SELECT o.id AS order_id, o.created_at, o.status, u.username, oi.product_id, oi.quantity, oi.price 
              FROM Orders o
              JOIN Users u ON o.user_id = u.id
              JOIN OrderItems oi ON o.id = oi.order_id";
    $result = $conn->query($query);

    // Deschidem fișierul pentru scrierea CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="comenzi.csv"');
    $output = fopen('php://output', 'w');

    // Scriem header-ul
    fputcsv($output, ['Order ID', 'Data', 'Status', 'Client', 'Produs ID', 'Cantitate', 'Preț']);

    // Scriem datele
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comenzile mele</title>
</head>
<body>
    <h2>Comenzile mele</h2>

    <table border="1">
        <thead>
            <tr>
                <th>ID Comandă</th>
                <th>Total</th>
                <th>Status</th>
                <th>Data</th>
                <th>Detalii</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['total']); ?> RON</td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                    <td>
                        <a href="view_order_details.php?order_id=<?php echo htmlspecialchars($order['id']); ?>">Vezi detalii</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <form method="POST">
    <button type="submit" name="export_csv">Exportă în CSV</button>
</form>
</body>
</html>
