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
    $query = "SELECT price, quantity FROM Products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $item['product_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Verificăm dacă produsul este pe stoc
    if ($product['quantity'] < $item['quantity']) {
        echo "Nu sunt suficiente produse pe stoc pentru produsul: " . $product['name'];
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

// Actualizăm stocul produselor
foreach ($cart as $item) {
    $query = "UPDATE Products SET quantity = quantity - ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
    $stmt->execute();

    // Inserăm produsele în OrderItems
    $query = "INSERT INTO OrderItems (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    $stmt->execute();
}

// Trimiterea notificării prin email
$query = "SELECT email FROM Users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$to = $user['email'];
$subject = "Comanda ta a fost plasată";
$message = "Comanda ta a fost plasată cu succes! Vei primi o notificare când comanda este procesată.";
$headers = "From: noreply@hipermarket.com";

mail($to, $subject, $message, $headers);

echo "Comanda a fost plasată cu succes!";
?>
