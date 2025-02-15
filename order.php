<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Obținem produsele disponibile pe stoc
$query = "SELECT * FROM Products WHERE quantity > 0"; // Afișăm doar produsele care sunt pe stoc
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plasează comanda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Selectează produsele dorite</h2>

    <form action="place_order.php" method="POST">
        <table>
            <tr>
                <th>Produs</th>
                <th>Cantitate</th>
                <th>Preț</th>
                <th>Adaugă în coș</th>
            </tr>
            <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['quantity']; ?> pe stoc</td>
                    <td><?php echo $product['price']; ?> RON</td>
                    <td>
                        <input type="number" name="product[<?php echo $product['id']; ?>][quantity]" 
                               min="1" max="<?php echo $product['quantity']; ?>" 
                               placeholder="Cantitate" required>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <button type="submit">Plasează comanda</button>
    </form>
</body>
</html>
