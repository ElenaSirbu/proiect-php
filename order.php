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
    <script>
        // Funcție pentru calculul sumei totale
        function calculateTotal() {
            let total = 0;
            const inputs = document.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                const productId = input.name.match(/\[(\d+)\]\[quantity\]/)[1];
                const price = parseFloat(document.getElementById(`price_${productId}`).innerText);
                const quantity = parseInt(input.value) || 0;
                total += price * quantity;
            });
            document.getElementById('total').innerText = total.toFixed(2) + ' RON';
        }

        // Ascultăm modificările în câmpurile de cantitate
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                input.addEventListener('input', calculateTotal);
            });
        });
    </script>
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
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?> pe stoc</td>
                    <td><span id="price_<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['price']); ?></span> RON</td>
                    <td>
                        <input type="number" name="product[<?php echo $product['id']; ?>][quantity]" 
                               min="1" max="<?php echo $product['quantity']; ?>" 
                               placeholder="Cantitate">
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <p><strong>Total: </strong><span id="total">0.00 RON</span></p>
        <button type="submit">Plasează comanda</button>
    </form>
</body>
</html>