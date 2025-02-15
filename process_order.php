<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'angajat' && $_SESSION['role'] != 'administrator')) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['order_id']) && isset($_GET['status'])) {
    $order_id = $_GET['order_id'];
    $status = $_GET['status'];

    // Actualizăm statusul comenzii
    $query = "UPDATE Orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    // Obținem email-ul clientului
    $query = "SELECT email FROM Users WHERE id = (SELECT user_id FROM Orders WHERE id = ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Trimitem notificarea prin email
    $to = $user['email'];
    $subject = "Status comanda actualizat";
    $message = "Statusul comenzii tale a fost actualizat la: $status.";
    $headers = "From: noreply@hipermarket.com";
    mail($to, $subject, $message, $headers);

    echo "Statusul comenzii a fost actualizat!";
}
?>
