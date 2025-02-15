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

if (isset($_POST['product'])) {
    foreach ($_POST['product'] as $product_id => $details) {
        if ($details['quantity'] > 0) {
            $cart[] = [
                'product_id' => $product_id,
                'quantity' => $details['quantity']
            ];
        }
    }
}

if (count($cart) === 0) {
    echo "Nu ai selectat produse!";
    exit;
}

// Calculăm totalul comenzii
foreach ($cart as $item) {
    $query = "SELECT price FROM Products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $item['product_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $total += $product['price'] * $item['quantity'];
}

// Inserăm comanda în baza de date
$query = "INSERT INTO Orders (user_id, total, status) VALUES (?, ?, ?)";
$status = "plasată"; // Statusul inițial al comenzii
$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo "Eroare la pregătirea interogării: " . $conn->error;
    exit;
}

$stmt->bind_param("ids", $user_id, $total, $status);
if (!$stmt->execute()) {
    echo "Eroare la executarea interogării: " . $stmt->error;
    exit;
}

$order_id = $stmt->insert_id; // ID-ul comenzii plasate

// Adăugăm produsele în OrderItems
foreach ($cart as $item) {
    $query = "INSERT INTO OrderItems (order_id, product_id, quantity, price) 
              SELECT ?, ?, ?, price FROM Products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['product_id']);
    $stmt->execute();
}
if ($stmt->execute()) {
    echo "Comanda a fost plasată cu succes!";
} else {
    echo "Eroare la plasarea comenzii: " . $stmt->error;
}
?>
