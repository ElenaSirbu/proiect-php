<?php 
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat și are rolul de client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Verificăm dacă utilizatorul a selectat produse
$cart = [];
$total = 0;

if (!isset($_POST['product']) || empty($_POST['product'])) {
    echo "Nu ai selectat produse!";
    exit;
}

// Procesăm produsele din coș
foreach ($_POST['product'] as $product_id => $details) {
    if (isset($details['quantity']) && $details['quantity'] > 0) {
        $cart[] = [
            'product_id' => $product_id,
            'quantity' => $details['quantity']
        ];
    }
}

if (count($cart) === 0) {
    echo "Nu ai selectat produse!";
    exit;
}

// Calculăm totalul comenzii
foreach ($cart as $item) {
    $query = "SELECT price, quantity, name FROM Products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $item['product_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Verificăm dacă produsul este pe stoc
    if ($product['quantity'] < $item['quantity']) {
        echo "Nu sunt suficiente produse pe stoc pentru produsul: " . htmlspecialchars($product['name']);
        exit;
    }

    $total += $product['price'] * $item['quantity'];
}

// Inserăm comanda în baza de date
$query = "INSERT INTO Orders (user_id, total) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("id", $user_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;  // Obținem ID-ul comenzii

// Actualizăm stocul produselor și inserăm în OrderItems
foreach ($cart as $item) {
    // Actualizăm stocul
    $query = "UPDATE Products SET quantity = quantity - ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
    $stmt->execute();

    // Obținem prețul produsului
    $query = "SELECT price FROM Products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $item['product_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Inserăm în OrderItems
    $query = "INSERT INTO OrderItems (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $product['price']);
    $stmt->execute();
}

// Afișăm mesajul de succes
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda Plasată - Hipermarket</title>

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Animații CSS -->
    <style>
        @keyframes fadeInBounce {
            0% { opacity: 0; transform: scale(0); }
            50% { opacity: 1; transform: scale(1.1); }
            100% { opacity: 1; transform: scale(1); }
        }

        .alert {
            animation: fadeInBounce 1.5s ease-out;
        }
    </style>

</head>
<body class="bg-light">

    <!-- Sunet de succes -->
    <audio id="success-sound" src="success.mp3" preload="auto"></audio>
    <script>
        window.onload = function() {
            document.getElementById("success-sound").play(); // Redăm sunetul la încărcarea paginii
        };
    </script>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Mesajul de succes cu animație -->
                <div class="alert alert-success text-center">
                    <h4>Comanda a fost plasată cu succes!</h4>
                    <p>Totalul comenzii este: <strong><?php echo number_format($total, 2); ?> RON</strong></p>
                    <p>Mulțumim pentru achiziție!</p>
                </div>

                <!-- Buton Înapoi la Dashboard -->
                <a href="dashboard.php" class="btn btn-secondary btn-block mb-2">Înapoi la Dashboard</a>
                <!-- Buton pentru vizualizarea comenzilor -->
                <a href="view_orders.php" class="btn btn-primary btn-block">Vizualizează comenzile</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS și jQuery (CDN) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
