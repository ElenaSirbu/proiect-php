<?php
session_start();

// Verificăm dacă utilizatorul este logat
if (!isset($_SESSION['user_id'])) {
    // Dacă nu este logat, redirecționăm la pagina principală
    header("Location: login.php");
    exit;
}

// CSRF Token - generăm un token pentru a preveni CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verificăm dacă este o cerere POST și token-ul CSRF este valid
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF invalid");
    }

    // Eliberăm variabilele de sesiune
    session_unset(); 
    // Distrugem sesiunea
    session_destroy(); 
    // Redirecționăm utilizatorul la pagina principală
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deconectare - Hipermarket</title>

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title text-center mb-4">Te-ai deconectat!</h1>
                        <p class="text-center">Ai fost deconectat cu succes din contul tău.</p>

                        <form method="POST" action="logout.php">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-block">Mergi la pagina principală</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS și jQuery (CDN) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
