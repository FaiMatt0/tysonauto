<?php
/**
 * Sanitizza l'input utente
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Genera un nome di file unico per le immagini
 * @param string $originalName
 * @return string
 */
function generateUniqueFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Formatta il prezzo per la visualizzazione
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return '€ ' . number_format($price, 0, ',', '.');
}

/**
 * Ottiene le categorie di un'auto
 * @param int $carId
 * @param mysqli $conn
 * @return array
 */
function getCarCategories($carId, $conn) {
    $categories = [];
    $sql = "SELECT c.id, c.name FROM categories c 
            JOIN car_categories cc ON c.id = cc.category_id 
            WHERE cc.car_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    return $categories;
}

/**
 * Ottiene tutte le categorie disponibili
 * @param mysqli $conn
 * @return array
 */
function getAllCategories($conn) {
    $categories = [];
    $result = $conn->query("SELECT id, name FROM categories ORDER BY name");
    
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    return $categories;
}

/**
 * Ottiene tutte le immagini di un'auto
 * @param int $carId
 * @param mysqli $conn
 * @return array
 */
function getCarImages($carId, $conn) {
    $images = [];
    $sql = "SELECT id, image_path, is_main FROM car_images WHERE car_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    return $images;
}

/**
 * Ottiene l'immagine principale di un'auto
 * @param int $carId
 * @param mysqli $conn
 * @return string|null
 */
function getMainCarImage($carId, $conn) {
    $sql = "SELECT image_path FROM car_images WHERE car_id = ? AND is_main = 1 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['image_path'];
    }
    
    // Se non c'è un'immagine principale, prendi la prima disponibile
    $sql = "SELECT image_path FROM car_images WHERE car_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['image_path'];
    }
    
    return null;
}