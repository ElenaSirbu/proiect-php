<?php
require('fpdf/fpdf.php'); // Include biblioteca FPDF
include 'db_config.php';

// Creăm un obiect FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Titlu
$pdf->Cell(200, 10, 'Comenzi - Raport', 0, 1, 'C');

// Titluri coloane
$pdf->Cell(40, 10, 'ID Comanda', 1);
$pdf->Cell(40, 10, 'User ID', 1);
$pdf->Cell(40, 10, 'Total', 1);
$pdf->Cell(40, 10, 'Status', 1);
$pdf->Cell(40, 10, 'Dată', 1);
$pdf->Ln();

// Obține datele din baza de date
$query = "SELECT * FROM Orders";
$result = $conn->query($query);

// Scrie datele în PDF
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['id'], 1);
    $pdf->Cell(40, 10, $row['user_id'], 1);
    $pdf->Cell(40, 10, $row['total'], 1);
    $pdf->Cell(40, 10, $row['status'], 1);
    $pdf->Cell(40, 10, $row['created_at'], 1);
    $pdf->Ln();
}

// Trimite PDF-ul la browser
$pdf->Output('D', 'comenzi.pdf');
?>
