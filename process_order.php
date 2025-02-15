<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat și dacă are rolul de angajat sau administrator
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'angajat' && $_SESSION['role'] != 'administrator')) {
    header("Location: login.php");
    exit;
}

// Funcție pentru trimiterea email-ului de notificare clientului
function sendStatusUpdateEmail($orderId, $status, $userEmail) {
    $subject = "Status comanda actualizat";
    $message = "Starea comenzii tale cu ID-ul $orderId a fost actualizată la: $status.";
    $headers = "From: no-reply@hipermarket.ro";

    mail($userEmail, $subject, $message, $headers);
}

// Preluăm toate comenzile
$query = "SELECT o.id, o.total, o.status, u.username, u.email FROM Orders o 
          JOIN Users u ON o.user_id = u.id";
$result = $conn->query($query);

// Procesăm modificarea statusului comenzii
if (isset($_GET['order_id']) && isset($_GET['status'])) {
    $orderId = $_GET['order_id'];
    $newStatus = $_GET['status'];

    // Verificăm dacă statusul este valid
    $validStatuses = ['procesată', 'livrată', 'anulată'];
    if (!in_array($newStatus, $validStatuses)) {
        echo "Status invalid!";
        exit;
    }

    // Actualizăm statusul comenzii în baza de date
    $updateQuery = "UPDATE Orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();

    // Obținem email-ul clientului pentru a trimite notificarea
    $emailQuery = "SELECT u.email FROM Orders o 
                   JOIN Users u ON o.user_id = u.id WHERE o.id = ?";
    $stmt = $conn->prepare($emailQuery);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $emailResult = $stmt->get_result();
    $userEmail = $emailResult->fetch_assoc()['email'];

    // Trimitem email clientului
    sendStatusUpdateEmail($orderId, $newStatus, $userEmail);

    // Redirecționăm înapoi pe pagina de procesare comenzi
    header("Location: process_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesare Comenzi - Hipermarket</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h2 class="text-center mb-4">Comenzi de Procesat</h2>

                <!-- Buton Înapoi la Dashboard -->
                <a href="dashboard.php" class="btn btn-secondary mb-3">Înapoi la Dashboard</a>

                <table class="table table-bordered">
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
                                    <!-- Linkuri pentru schimbarea statusului comenzii -->
                                    <a href="process_orders.php?order_id=<?php echo htmlspecialchars($order['id']); ?>&status=procesată" class="btn btn-warning btn-sm">Procesează</a> | 
                                    <a href="process_orders.php?order_id=<?php echo htmlspecialchars($order['id']); ?>&status=livrată" class="btn btn-success btn-sm">Livrată</a> | 
                                    <a href="process_orders.php?order_id=<?php echo htmlspecialchars($order['id']); ?>&status=anulată" class="btn btn-danger btn-sm">Anulează</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS și jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
