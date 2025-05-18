<?php
// Configurazione del database
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Cambia con il tuo username
define('DB_PASS', 'aaaa'); // Cambia con la tua password
define('DB_NAME', 'tysonautogarage');

// Crea connessione
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Imposta charset
$conn->set_charset("utf8");