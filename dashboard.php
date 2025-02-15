<?php
session_start();

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Dacă nu e autentificat, îl redirecționăm la login
    exit;
}

// Verificăm rolul utilizatorului din sesiune
$user_role = $_SESSION['role']; // Presupunem că rolul utilizatorului este stocat în sesiune
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Stilurile comune -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap -->
</head>
<body>

    <div class="container mt-5">
        <h2>Bine ai venit, <?php echo $_SESSION['username']; ?>!</h2>

        <nav>
            <ul class="list-group">
                <?php if ($user_role == 'client'): ?>
                    <!-- Opțiuni pentru client -->
                    <li class="list-group-item"><a href="search_products.php">Căutare produse</a></li>
                    <li class="list-group-item"><a href="cart.php">Coș</a></li>
                    <li class="list-group-item"><a href="profile.php">Informații personale</a></li>
                    <li class="list-group-item"><a href="view_orders.php">Comenzile mele</a></li>

                <?php elseif ($user_role == 'angajat'): ?>
                    <!-- Opțiuni pentru angajat -->
                    <li class="list-group-item"><a href="manage_products.php">Gestionare produse</a></li>
                    <li class="list-group-item"><a href="view_orders.php">Vizualizează comenzile</a></li>
                    <li class="list-group-item"><a href="customer_interactions.php">Interacțiuni cu clienții</a></li>
                    <li class="list-group-item"><a href="update_order.php">Actualizează comenzi</a></li>

                <?php elseif ($user_role == 'admin'): ?>
                    <!-- Opțiuni pentru admin -->
                    <li class="list-group-item"><a href="manage_users.php">Gestionare utilizatori</a></li>
                    <li class="list-group-item"><a href="reports.php">Rapoarte financiare</a></li>
                    <li class="list-group-item"><a href="sales_reports.php">Raport vânzări</a></li>
                    <li class="list-group-item"><a href="admin_settings.php">Setări admin</a></li>

                <?php endif; ?>
            </ul>
        </nav>

        <!-- Buton de logout -->
        <form action="logout.php" method="POST">
            <button type="submit" class="btn btn-danger mt-3">Logout</button>
        </form>
    </div>

    <!-- Scripturile necesare pentru Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
