<?php
// Includi il file di autenticazione
require_once '../includes/auth.php';

// Distruggi la sessione
session_unset();
session_destroy();

// Reindirizza alla pagina di login
header('Location: /pages/login.php');
exit;