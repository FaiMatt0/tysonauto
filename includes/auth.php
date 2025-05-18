<?php
// Se la sessione non è già attiva, avviala
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se l'utente è loggato
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Verifica se l'utente è admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Reindirizza l'utente alla pagina di login se non è loggato
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /pages/login.php');
        exit;
    }
}

/**
 * Reindirizza l'utente alla dashboard se non è admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /admin/dashboard.php');
        exit;
    }
}