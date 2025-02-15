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

// Verificăm dacă există produse selectate
if (isset($_POST['product'])) {
    foreach ($_POST['product'] as $product_id => $details) {
        if (isset($details['quantity']) && $details['quantity'] > 0) {
            $cart[] = [
                'product_id' => $product_id,
                'quantity' => $details['quantity']
            ];
        }
    }
}

// Verificăm dacă există produse în coș
if (count($cart) === 0) {
    echo "Nu ai selectat produse sau cantitățile sunt invalide!";
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

// Inserăm comanda în tabela Orders
$query = "INSERT INTO Orders (user_id, total, status) VALUES (?, ?, ?)";
$status = "plasată"; // Statusul inițial al comenzii
$stmt = $conn->prepare($query);
$stmt->bind_param("ids", $user_id, $total, $status);
$stmt->execute();
$order_id = $stmt->insert_id; // ID-ul comenzii plasate

// Adăugăm produsele în OrderItems
foreach ($cart as $item) {
    $query = "INSERT INTO OrderItems (order_id, product_id, quantity, price) 
              SELECT ?, ?, ?, price FROM Products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['product_id']);
    $stmt->execute();
}

// Actualizăm cantitatea produselor în tabela Products
foreach ($cart as $item) {
    $query = "UPDATE Products SET quantity = quantity - ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
    $stmt->execute();
}

echo "Comanda a fost plasată cu succes!";
?>
