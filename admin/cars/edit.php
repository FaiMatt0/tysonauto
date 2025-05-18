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

// Ottieni i dati dell'auto
$carQuery = "SELECT c.*, d.transmission, d.fuel_type 
            FROM cars c 
            LEFT JOIN car_details d ON c.id = d.car_id 
            WHERE c.id = ?";
$carStmt = $conn->prepare($carQuery);
$carStmt->bind_param("i", $carId);
$carStmt->execute();
$carResult = $carStmt->get_result();

if ($carResult->num_rows === 0) {
    header('Location: index.php?error=Auto non trovata');
    exit;
}

$car = $carResult->fetch_assoc();

// Ottieni le immagini dell'auto
$images = getCarImages($carId, $conn);

// Ottieni le categorie dell'auto
$carCategories = getCarCategories($carId, $conn);
$carCategoryIds = array_map(function($category) {
    return $category['id'];
}, $carCategories);

// Ottieni tutte le categorie disponibili
$allCategories = getAllCategories($conn);

// Processa form quando viene inviato
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Valida i dati del form
    $brand = sanitize($_POST['brand'] ?? '');
    $model = sanitize($_POST['model'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $color = sanitize($_POST['color'] ?? '');
    $mileage = (int)($_POST['mileage'] ?? 0);
    $price = (float)str_replace(',', '.', $_POST['price'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $transmission = sanitize($_POST['transmission'] ?? '');
    $fuel_type = sanitize($_POST['fuel_type'] ?? '');
    $selectedCategories = $_POST['categories'] ?? [];
    $mainImageId = isset($_POST['main_image']) ? (int)$_POST['main_image'] : 0;
    $deleteImages = isset($_POST['delete_images']) ? $_POST['delete_images'] : [];
    
    // Validazione
    if (empty($brand)) $errors[] = "La marca è obbligatoria.";
    if (empty($model)) $errors[] = "Il modello è obbligatorio.";
    if ($year < 1900 || $year > date('Y') + 1) $errors[] = "L'anno deve essere valido.";
    if ($mileage < 0) $errors[] = "Il chilometraggio non può essere negativo.";
    if ($price <= 0) $errors[] = "Il prezzo deve essere maggiore di zero.";
    
    // Se non ci sono errori, procedi con l'aggiornamento
    if (empty($errors)) {
        try {
            // Inizia la transazione
            $conn->begin_transaction();
            
            // 2. Aggiorna dati nella tabella cars
            $carSql = "UPDATE cars SET brand = ?, model = ?, year = ?, color = ?, 
                      mileage = ?, price = ?, description = ? WHERE id = ?";
            $carStmt = $conn->prepare($carSql);
            $carStmt->bind_param("ssissdsi", $brand, $model, $year, $color, $mileage, $price, $description, $carId);
            $carStmt->execute();
            
            // 3. Aggiorna dati nella tabella car_details
            // Controlla se esiste già un record in car_details
            $checkDetailsSql = "SELECT * FROM car_details WHERE car_id = ?";
            $checkDetailsStmt = $conn->prepare($checkDetailsSql);
            $checkDetailsStmt->bind_param("i", $carId);
            $checkDetailsStmt->execute();
            $detailsResult = $checkDetailsStmt->get_result();
            
            if ($detailsResult->num_rows > 0) {
                // Aggiorna record esistente
                $detailsSql = "UPDATE car_details SET transmission = ?, fuel_type = ? WHERE car_id = ?";
                $detailsStmt = $conn->prepare($detailsSql);
                $detailsStmt->bind_param("ssi", $transmission, $fuel_type, $carId);
            } else {
                // Inserisci nuovo record
                $detailsSql = "INSERT INTO car_details (car_id, transmission, fuel_type) VALUES (?, ?, ?)";
                $detailsStmt = $conn->prepare($detailsSql);
                $detailsStmt->bind_param("iss", $carId, $transmission, $fuel_type);
            }
            $detailsStmt->execute();
            
            // 4. Aggiorna le categorie
            // Elimina tutte le associazioni di categorie esistenti
            $deleteCategories = "DELETE FROM car_categories WHERE car_id = ?";
            $deleteCategoriesStmt = $conn->prepare($deleteCategories);
            $deleteCategoriesStmt->bind_param("i", $carId);
            $deleteCategoriesStmt->execute();
            
            // Inserisci nuove associazioni di categorie
            if (!empty($selectedCategories)) {
                $categoryInsertSql = "INSERT INTO car_categories (car_id, category_id) VALUES (?, ?)";
                $categoryInsertStmt = $conn->prepare($categoryInsertSql);
                
                foreach ($selectedCategories as $categoryId) {
                    $categoryInsertStmt->bind_param("ii", $carId, $categoryId);
                    $categoryInsertStmt->execute();
                }
            }
            
            // 5. Gestisci eliminazione immagini
            if (!empty($deleteImages)) {
                foreach ($deleteImages as $imageId) {
                    // Ottieni info sull'immagine prima di eliminarla
                    $getImageSql = "SELECT image_path FROM car_images WHERE id = ? AND car_id = ?";
                    $getImageStmt = $conn->prepare($getImageSql);
                    $getImageStmt->bind_param("ii", $imageId, $carId);
                    $getImageStmt->execute();
                    $imageResult = $getImageStmt->get_result();
                    
                    if ($imageRow = $imageResult->fetch_assoc()) {
                        $imagePath = '../../uploads/cars/' . $imageRow['image_path'];
                        // Elimina il file se esiste
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                        
                        // Elimina il record dal database
                        $deleteImageSql = "DELETE FROM car_images WHERE id = ? AND car_id = ?";
                        $deleteImageStmt = $conn->prepare($deleteImageSql);
                        $deleteImageStmt->bind_param("ii", $imageId, $carId);
                        $deleteImageStmt->execute();
                    }
                }
            }
            
            // 6. Imposta l'immagine principale
            if ($mainImageId > 0) {
                // Prima resetta tutte le immagini a non principali
                $resetMainSql = "UPDATE car_images SET is_main = 0 WHERE car_id = ?";
                $resetMainStmt = $conn->prepare($resetMainSql);
                $resetMainStmt->bind_param("i", $carId);
                $resetMainStmt->execute();
                
                // Poi imposta l'immagine selezionata come principale
                $setMainSql = "UPDATE car_images SET is_main = 1 WHERE id = ? AND car_id = ?";
                $setMainStmt = $conn->prepare($setMainSql);
                $setMainStmt->bind_param("ii", $mainImageId, $carId);
                $setMainStmt->execute();
            }
            
            // 7. Gestisci il caricamento di nuove immagini
            if (!empty($_FILES['images']['name'][0])) {
                // Assicurati che la directory per gli upload esista
                $uploadDir = '../../uploads/cars/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Inserisci ogni immagine
                $imageSql = "INSERT INTO car_images (car_id, image_path, is_main) VALUES (?, ?, ?)";
                $imageStmt = $conn->prepare($imageSql);
                
                // Se non ci sono immagini esistenti, imposta la prima come principale
                $hasExistingImages = count($images) > 0;
                
                for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
                    // Se il file è stato caricato correttamente
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES['images']['tmp_name'][$i];
                        $fileName = generateUniqueFilename($_FILES['images']['name'][$i]);
                        $uploadPath = $uploadDir . $fileName;
                        
                        // Sposta il file dalla directory temporanea alla destinazione
                        if (move_uploaded_file($tmpName, $uploadPath)) {
                            // Imposta la prima immagine come principale solo se non ci sono immagini esistenti
                            $isMain = (!$hasExistingImages && $i === 0) ? 1 : 0;
                            
                            $imageStmt->bind_param("isi", $carId, $fileName, $isMain);
                            $imageStmt->execute();
                        }
                    }
                }
            }
            
            // Commit della transazione
            $conn->commit();
            
            $success = true;
            
            // Aggiorna i dati dell'auto e le immagini dopo il salvataggio
            $carStmt->execute();
            $carResult = $carStmt->get_result();
            $car = $carResult->fetch_assoc();
            $images = getCarImages($carId, $conn);
            
            // Reindirizza alla lista auto dopo aver modificato con successo
            header('Location: index.php?success=Auto modificata con successo');
            exit;
            
        } catch (Exception $e) {
            // Rollback in caso di errore
            $conn->rollback();
            $errors[] = "Errore durante il salvataggio: " . $e->getMessage();
        }
    }
}

$pageTitle = "Modifica Auto - Tyson Autogarage";
$additionalCss = "/styles/admin.css";
include '../../includes/header.php';
?>

<div class="admin-container">
    <div class="content">
        <div class="admin-header">
            <h1 class="admin-title">Modifica Auto</h1>
            <a href="/admin/cars/index.php" class="button secondary back-button">
                <i class="fas fa-arrow-left"></i> Torna alla lista
            </a>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert success">
                Auto modificata con successo!
            </div>
        <?php endif; ?>
        
        <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="brand">Marca *</label>
                    <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($car['brand']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="model">Modello *</label>
                    <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Anno *</label>
                    <input type="number" id="year" name="year" value="<?php echo $car['year']; ?>" min="1900" max="<?php echo date('Y') + 1; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="color">Colore</label>
                    <input type="text" id="color" name="color" value="<?php echo htmlspecialchars($car['color'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="mileage">Chilometraggio *</label>
                    <input type="number" id="mileage" name="mileage" value="<?php echo $car['mileage']; ?>" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Prezzo (€) *</label>
                    <input type="text" id="price" name="price" value="<?php echo $car['price']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="transmission">Trasmissione</label>
                    <select id="transmission" name="transmission">
                        <option value="">Seleziona...</option>
                        <option value="manual" <?php echo ($car['transmission'] === 'manual') ? 'selected' : ''; ?>>Manuale</option>
                        <option value="automatic" <?php echo ($car['transmission'] === 'automatic') ? 'selected' : ''; ?>>Automatico</option>
                        <option value="semi-automatic" <?php echo ($car['transmission'] === 'semi-automatic') ? 'selected' : ''; ?>>Semi-automatico</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="fuel_type">Carburante</label>
                    <select id="fuel_type" name="fuel_type">
                        <option value="">Seleziona...</option>
                        <option value="petrol" <?php echo ($car['fuel_type'] === 'petrol') ? 'selected' : ''; ?>>Benzina</option>
                        <option value="diesel" <?php echo ($car['fuel_type'] === 'diesel') ? 'selected' : ''; ?>>Diesel</option>
                        <option value="electric" <?php echo ($car['fuel_type'] === 'electric') ? 'selected' : ''; ?>>Elettrico</option>
                        <option value="hybrid" <?php echo ($car['fuel_type'] === 'hybrid') ? 'selected' : ''; ?>>Ibrido</option>
                        <option value="lpg" <?php echo ($car['fuel_type'] === 'lpg') ? 'selected' : ''; ?>>GPL</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Descrizione</label>
                <textarea id="description" name="description" rows="6"><?php echo htmlspecialchars($car['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Categorie</label>
                <div class="categories-grid">
                    <?php foreach ($allCategories as $category): ?>
                        <div class="category-checkbox">
                            <input type="checkbox" id="category_<?php echo $category['id']; ?>" name="categories[]" value="<?php echo $category['id']; ?>"
                                <?php echo (in_array($category['id'], $carCategoryIds)) ? 'checked' : ''; ?>>
                            <label for="category_<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label>Immagini Attuali</label>
                <?php if (empty($images)): ?>
                    <p>Nessuna immagine disponibile.</p>
                <?php else: ?>
                    <div class="current-images">
                        <?php foreach ($images as $image): ?>
                            <div class="car-image-item">
                                <div class="image-container">
                                    <img src="/uploads/cars/<?php echo $image['image_path']; ?>" alt="Immagine Auto">
                                    <?php if ($image['is_main']): ?>
                                        <div class="main-badge">Principale</div>
                                    <?php endif; ?>
                                </div>
                                <div class="image-controls">
                                    <label>
                                        <input type="radio" name="main_image" value="<?php echo $image['id']; ?>" <?php echo ($image['is_main']) ? 'checked' : ''; ?>>
                                        Imposta come principale
                                    </label>
                                    <label>
                                        <input type="checkbox" name="delete_images[]" value="<?php echo $image['id']; ?>">
                                        Elimina
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="images">Aggiungi Nuove Immagini</label>
                <div class="image-upload-container">
                    <div class="image-preview-container" id="imagePreviewContainer"></div>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple class="image-upload">
                    <label for="images" class="image-upload-label">
                        <i class="fas fa-upload"></i> Seleziona Immagini
                    </label>
                    <div class="image-upload-instructions">
                        <p>Puoi selezionare più file contemporaneamente.</p>
                        <p>Formati supportati: JPG, PNG, GIF</p>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="button primary">Aggiorna Auto</button>
                <a href="/admin/cars/index.php" class="button secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>

<script>
// Gestione anteprima immagini
document.getElementById('images').addEventListener('change', function() {
    const container = document.getElementById('imagePreviewContainer');
    container.innerHTML = '';
    
    if (this.files) {
        const fragment = document.createDocumentFragment();
        
        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            
            // Crea un div per l'anteprima
            const previewDiv = document.createElement('div');
            previewDiv.className = 'image-preview';
            
            // Crea l'elemento img
            const img = document.createElement('img');
            
            // Imposta l'anteprima usando URL.createObjectURL
            img.src = URL.createObjectURL(file);
            img.onload = function() {
                // Rilascia l'oggetto URL quando l'immagine è caricata
                URL.revokeObjectURL(this.src);
            };
            
            previewDiv.appendChild(img);
            fragment.appendChild(previewDiv);
        }
        
        container.appendChild(fragment);
    }
});
</script>

<?php include '../../includes/footer.php'; ?>