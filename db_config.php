<?php

// Preluăm URL-ul din variabila de mediu
$cleardb_url = parse_url(getenv("JAWSDB_URL"));

// Verificăm dacă URL-ul este valid și toate valorile sunt setate
if (!$cleardb_url || !isset($cleardb_url["host"], $cleardb_url["user"], $cleardb_url["pass"], $cleardb_url["path"])) {
    error_log("Eroare la procesarea URL-ului JawsDB.");
    die("Eroare de conectare la baza de date.");
}

// Extragem valorile pentru host, user, parolă și baza de date
$host = $cleardb_url["host"];
$user = $cleardb_url["user"];
$pass = $cleardb_url["pass"];
$db = substr($cleardb_url["path"], 1); // Îndepărtăm slash-ul inițial

// Validăm datele de conexiune
if (empty($host) || empty($user) || empty($pass) || empty($db)) {
    error_log("Informațiile de conectare sunt incomplete.");
    die("Informații incomplete pentru conexiune.");
}

// Creăm conexiunea la baza de date
$conn = new mysqli($host, $user, $pass, $db);

// Verificăm dacă conexiunea este reușită
if ($conn->connect_error) {
    // Logăm eroarea în loc să o afișăm utilizatorului
    error_log("Conexiunea la baza de date a eșuat: " . $conn->connect_error);
    die("Eroare la conectarea la baza de date.");
}


?>
