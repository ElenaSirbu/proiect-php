<?php
include 'db_config.php';

// Setează header-ul pentru a indica faptul că fișierul este CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="comenzi.csv"');

// Deschide fișierul CSV în mod de scriere
$output = fopen('php://output', 'w');

// Scrie titlurile coloanelor
fputcsv($output, ['ID Comandă', 'User ID', 'Total', 'Status', 'Dată']);

// Obține datele din baza de date
$query = "SELECT * FROM Orders";
$result = $conn->query($query);

// Scrie fiecare rând de date în fișierul CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Închide fișierul CSV
fclose($output);
?>
