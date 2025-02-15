<?php
include 'db_config.php';
session_start();

// Verificăm dacă utilizatorul este autentificat și are rol de admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header("Location: dashboard.php");
    exit;
}

// Generăm și stocăm token CSRF dacă nu există deja
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$result = $conn->query("SELECT * FROM Categories");
echo "<div class='container mt-5'>
        <h2>Lista Categoriilor</h2>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nume</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>";

while ($row = $result->fetch_assoc()) {
    // Protejăm datele de XSS
    $id = htmlspecialchars($row['id']);
    $name = htmlspecialchars($row['name']);
    
    // Creăm linkuri cu parametrii securizați
    $edit_link = "categories_update.php?id=" . urlencode($id);
    $delete_link = "categories_delete.php?id=" . urlencode($id);

    echo "<tr><td>{$id}</td><td>{$name}</td>
    <td><a href='{$edit_link}' class='btn btn-warning btn-sm'>Edit</a> 
        <a href='{$delete_link}' class='btn btn-danger btn-sm'>Șterge</a></td></tr>";
}

echo "</tbody></table>";

echo "<a href='logout.php' class='btn btn-danger mb-3'>Deconectează-te</a>";
echo "<a href='dashboard.php' class='btn btn-secondary mb-3'>Înapoi la Dashboard</a>";

echo "</div>";


?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>