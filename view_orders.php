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

// Export CSV
// Export CSV
if (isset($_POST['export_csv'])) {
    // Selectăm comenzile și detaliile acestora
    $query = "SELECT o.id AS order_id, o.created_at, o.status, u.username, oi.product_id, oi.quantity, oi.price 
              FROM Orders o
              JOIN Users u ON o.user_id = u.id
              JOIN OrderItems oi ON o.id = oi.id";
    $result_csv = $conn->query($query);

    if ($result_csv->num_rows > 0) {  // Verificăm dacă există date
        // Deschidem fișierul pentru scrierea CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="comenzi.csv"');
        $output = fopen('php://output', 'w');

        // Scriem header-ul
        fputcsv($output, ['Order ID', 'Data', 'Status', 'Client', 'Produs ID', 'Cantitate', 'Preț']);

        // Scriem datele
        while ($row = $result_csv->fetch_assoc()) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    } else {
        echo "Nu există date pentru export.";
    }
}


// Export PDF
// Export PDF
if (isset($_POST['export_pdf'])) {
    // Selectăm comenzile și detaliile acestora
    $query = "SELECT o.id AS order_id, o.created_at, o.status, u.username, oi.product_id, oi.quantity, oi.price 
              FROM Orders o
              JOIN Users u ON o.user_id = u.id
              JOIN OrderItems oi ON o.id = oi.id";
    $result_pdf = $conn->query($query);

    if ($result_pdf->num_rows > 0) {  // Verificăm dacă există date
        // Creăm instanța FPDF
        require('fpdf.php');
        $pdf = new FPDF();
        $pdf->AddPage();

        // Setăm fontul
        $pdf->SetFont('Arial', 'B', 12);

        // Adăugăm titlu
        $pdf->Cell(200, 10, 'Raport Comenzi', 0, 1, 'C');

        // Adăugăm header-ul
        $pdf->Cell(30, 10, 'Order ID', 1);
        $pdf->Cell(40, 10, 'Data', 1);
        $pdf->Cell(30, 10, 'Status', 1);
        $pdf->Cell(40, 10, 'Client', 1);
        $pdf->Cell(30, 10, 'Produs ID', 1);
        $pdf->Cell(30, 10, 'Cantitate', 1);
        $pdf->Cell(30, 10, 'Preț', 1);
        $pdf->Ln();

        // Adăugăm datele
        while ($row = $result_pdf->fetch_assoc()) {
            $pdf->Cell(30, 10, $row['order_id'], 1);
            $pdf->Cell(40, 10, $row['created_at'], 1);
            $pdf->Cell(30, 10, $row['status'], 1);
            $pdf->Cell(40, 10, $row['username'], 1);
            $pdf->Cell(30, 10, $row['product_id'], 1);
            $pdf->Cell(30, 10, $row['quantity'], 1);
            $pdf->Cell(30, 10, $row['price'], 1);
            $pdf->Ln();
        }

        // Salvăm fișierul PDF
        $pdf->Output('D', 'comenzi.pdf');
        exit;
    } else {
        echo "Nu există date pentru export.";
    }
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

    <!-- Formular pentru export CSV -->
    <form method="POST">
        <button type="submit" name="export_csv">Exportă în CSV</button>
    </form>

    <!-- Formular pentru export PDF -->
    <form method="POST">
        <button type="submit" name="export_pdf">Exportă în PDF</button>
    </form>

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
</body>
</html>
