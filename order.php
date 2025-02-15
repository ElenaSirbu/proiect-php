<?php
session_start();
include 'db_config.php';

// Verificăm dacă utilizatorul este autentificat și este client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

// Procesare căutare și sortare produse
$search = '';
$orderBy = 'name'; // Sortare după nume implicit

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search'])) {
        $search = $_POST['search'];
    }
    if (isset($_POST['sort'])) {
        $orderBy = $_POST['sort'];
    }
}

// Construim interogarea pentru a afișa produsele
$query = "SELECT * FROM Products WHERE quantity > 0 AND name LIKE '%$search%' ORDER BY $orderBy";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "Nu sunt produse disponibile pe stoc.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plasează comanda</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

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

        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                input.addEventListener('input', calculateTotal);
            });
        });
    </script>
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center">Selectează produsele dorite</h2>

                <!-- Formular de căutare și sortare -->
                <form method="POST" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Căutare produs" value="<?= htmlspecialchars($search) ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">Căutare</button>
                        </div>
                    </div>
                </form>

                <form method="POST" class="mb-4">
                    <label for="sort">Sortează după: </label>
                    <select name="sort" id="sort" class="form-control w-25 d-inline-block">
                        <option value="name" <?= $orderBy == 'name' ? 'selected' : '' ?>>Nume</option>
                        <option value="price" <?= $orderBy == 'price' ? 'selected' : '' ?>>Preț</option>
                    </select>
                    <button class="btn btn-outline-secondary" type="submit">Aplică sortarea</button>
                </form>

                <!-- Tabel produse -->
                <form action="place_order.php" method="POST">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Produs</th>
                                <th>Cantitate</th>
                                <th>Preț</th>
                                <th>Detalii</th>
                                <th>Adaugă în coș</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['quantity']); ?> pe stoc</td>
                                    <td><span id="price_<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['price']); ?></span> RON</td>
                                    <td>
                                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-info btn-sm">Vezi detalii</a>
                                    </td>
                                    <td>
                                        <input type="number" name="product[<?php echo $product['id']; ?>][quantity]" 
                                               min="1" max="<?php echo $product['quantity']; ?>" 
                                               placeholder="Cantitate" class="form-control">
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <p><strong>Total: </strong><span id="total">0.00 RON</span></p>
                    <button type="submit" class="btn btn-success">Plasează comanda</button>
                </form>

                <a href="dashboard.php" class="btn btn-secondary">Înapoi la dashboard</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS și jQuery (CDN) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
