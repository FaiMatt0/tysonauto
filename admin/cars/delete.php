<?php
// Includi file di configurazione e funzioni
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Verifica login
requireLogin();

// Verifica se è stato fornito un ID valido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=ID auto non valido');
    exit;
}

$carId = (int)$_GET['id'];

// Verifica che l'auto esista
$checkSql = "SELECT id FROM cars WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $carId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    header('Location: index.php?error=Auto non trovata');
    exit;
}

try {
    // Inizia la transazione
    $conn->begin_transaction();
    
    // 1. Elimina le immagini fisiche dal server
    $imagesSql = "SELECT image_path FROM car_images WHERE car_id = ?";
    $imagesStmt = $conn->prepare($imagesSql);
    $imagesStmt->bind_param("i", $carId);
    $imagesStmt->execute();
    $imagesResult = $imagesStmt->get_result();
    
    while ($image = $imagesResult->fetch_assoc()) {
        $imagePath = '../../uploads/cars/' . $image['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // 2. Elimina le associazioni con le categorie
    $deleteCategoriesSql = "DELETE FROM car_categories WHERE car_id = ?";
    $deleteCategoriesStmt = $conn->prepare($deleteCategoriesSql);
    $deleteCategoriesStmt->bind_param("i", $carId);
    $deleteCategoriesStmt->execute();
    
    // 3. Elimina i dettagli dell'auto
    $deleteDetailsSql = "DELETE FROM car_details WHERE car_id = ?";
    $deleteDetailsStmt = $conn->prepare($deleteDetailsSql);
    $deleteDetailsStmt->bind_param("i", $carId);
    $deleteDetailsStmt->execute();
    
    // 4. Elimina le immagini dal database
    $deleteImagesSql = "DELETE FROM car_images WHERE car_id = ?";
    $deleteImagesStmt = $conn->prepare($deleteImagesSql);
    $deleteImagesStmt->bind_param("i", $carId);
    $deleteImagesStmt->execute();
    
    // 5. Elimina l'auto
    $deleteCarSql = "DELETE FROM cars WHERE id = ?";
    $deleteCarStmt = $conn->prepare($deleteCarSql);
    $deleteCarStmt->bind_param("i", $carId);
    $deleteCarStmt->execute();
    
    // Commit della transazione
    $conn->commit();
    
    // Reindirizza con messaggio di successo
    header('Location: index.php?success=Auto eliminata con successo');
    exit;
    
} catch (Exception $e) {
    // Rollback in caso di errore
    $conn->rollback();
    
    // Reindirizza con messaggio di errore
    header('Location: index.php?error=Errore durante l\'eliminazione: ' . $e->getMessage());
    exit;
}