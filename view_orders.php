<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verificăm rolul utilizatorului
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Permitem acces doar clientului sau angajatului
if ($role !== 'client' && $role !== 'angajat') {
    echo "Acces interzis. Numai clienții și angajații pot vizualiza comenzile.";
    exit;
}

// Obținem comenzile utilizatorului
$query = "SELECT id, total, status, created_at FROM Orders WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Verificăm dacă utilizatorul a solicitat exportul CSV
if (isset($_POST['export_csv']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Token CSRF pentru protecție
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF invalid.");
    }

    // Export CSV
    $query = "SELECT o.id AS order_id, DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i:%s') AS created_at, 
                 o.status, u.username, oi.product_id, oi.quantity, oi.price 
          FROM Orders o
          JOIN Users u ON o.user_id = u.id
          JOIN OrderItems oi ON o.id = oi.id";
    $result_csv = $conn->query($query);

    if ($result_csv->num_rows > 0) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="comenzi.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Order ID', 'Data', 'Status', 'Client', 'Produs ID', 'Cantitate', 'Preț']);

        while ($row = $result_csv->fetch_assoc()) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    } else {
        echo "Nu există date pentru export.";
    }
}

// Verificăm dacă utilizatorul a solicitat exportul PDF
if (isset($_POST['export_pdf']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Token CSRF pentru protecție
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF invalid.");
    }

    // Export PDF
    require('fpdf.php');
    $query = "SELECT o.id AS order_id, DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i:%s') AS created_at, 
                 o.status, u.username, oi.product_id, oi.quantity, oi.price 
          FROM Orders o
          JOIN Users u ON o.user_id = u.id
          JOIN OrderItems oi ON o.id = oi.id";
    $result_pdf = $conn->query($query);

    // Creăm instanța FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
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

    while ($row = $result_pdf->fetch_assoc()) {
        $pdf->Cell(30, 10, $row['order_id'], 1);
        $pdf->Cell(40, 10, date('Y-m-d H:i:s', strtotime($row['created_at'])), 1);

        $pdf->Cell(30, 10, $row['status'], 1);
        $pdf->Cell(40, 10, $row['username'], 1);
        $pdf->Cell(30, 10, $row['product_id'], 1);
        $pdf->Cell(30, 10, $row['quantity'], 1);
        $pdf->Cell(30, 10, $row['price'], 1);
        $pdf->Ln();
    }

    ob_end_clean(); // Elimină orice output anterior pentru a preveni coruperea PDF-ului
$pdf->Output('D', 'comenzi.pdf');
exit;
}

// Generăm token CSRF pentru protecție
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comenzile mele</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Comenzile mele</h2>

        <form method="POST" class="mb-3">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" name="export_csv" class="btn btn-primary">Exportă în CSV</button>
            <button type="submit" name="export_pdf" class="btn btn-success">Exportă în PDF</button>
        </form>

        <table class="table table-bordered">
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
                            <a href="view_order_details.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-info">Vezi detalii</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary">Înapoi la dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
