<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Preluăm produsele din baza de date
$query = "SELECT * FROM Products";
$result = $conn->query($query);

// Verificăm dacă formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preluăm datele comenzii și item-urilor
    $user_id = $_SESSION['user_id'];
    $total = 0; // Vom calcula totalul comenzii

    // Cream o comandă în tabela Orders
    $stmt = $conn->prepare("INSERT INTO Orders (user_id, total, status) VALUES (?, ?, 'plasată')");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Obținem ID-ul comenzii plasate

    // Preluăm produsele selectate din formular
    foreach ($_POST['product_id'] as $index => $product_id) {
        $quantity = $_POST['quantity'][$index];
        
        // Verificăm dacă produsul există în baza de date
        $stmt = $conn->prepare("SELECT * FROM Products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product_result = $stmt->get_result();
        $product = $product_result->fetch_assoc();

        if ($product) {
            // Calculăm prețul total pentru fiecare produs
            $price = $product['price'];
            $item_total = $price * $quantity;
            $total += $item_total;

            // Adăugăm item-ul în tabela OrderItems
            $stmt = $conn->prepare("INSERT INTO OrderItems (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
        }
    }

    // Actualizăm totalul comenzii
    $stmt = $conn->prepare("UPDATE Orders SET total = ? WHERE id = ?");
    $stmt->bind_param("di", $total, $order_id);
    $stmt->execute();

    echo "Comanda a fost plasată cu succes!";
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plasează Comanda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Plasează Comanda</h2>
    <form method="POST">
        <h3>Produse disponibile</h3>
        <table>
            <tr>
                <th>Produse</th>
                <th>Preț</th>
                <th>Cantitate</th>
            </tr>
            <?php while ($product = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td>
                        <input type="number" name="quantity[]" min="1" value="1">
                        <input type="hidden" name="product_id[]" value="<?php echo $product['id']; ?>">
                    </td>
                </tr>
            <?php } ?>
        </table>
        <button type="submit">Plasează Comanda</button>
    </form>
</body>
</html>
