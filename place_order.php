<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obținem produsele din formular
$cart = [];
$total = 0;

echo number_format($total, 2);

// Verificăm dacă utilizatorul a selectat produse
if (!isset($_POST['product']) || empty($_POST['product'])) {
    echo "Nu ai selectat produse!";
    exit;
}

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
$query = "INSERT INTO Orders (user_id, total, status) VALUES (?, ?, ?)";
$status = "plasată"; // Statusul inițial al comenzii
$stmt = $conn->prepare($query);
$stmt->bind_param("ids", $user_id, $total, $status);
$stmt->execute();
$order_id = $stmt->insert_id; // ID-ul comenzii plasate

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
echo "Comanda a fost plasată cu succes!";
?>
