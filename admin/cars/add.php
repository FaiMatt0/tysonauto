<?php
// Includi file di configurazione e funzioni
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Verifica login
requireLogin();

// Ottieni tutte le categorie
$categories = getAllCategories($conn);

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
    
    // Validazione
    if (empty($brand)) $errors[] = "La marca è obbligatoria.";
    if (empty($model)) $errors[] = "Il modello è obbligatorio.";
    if ($year < 1900 || $year > date('Y') + 1) $errors[] = "L'anno deve essere valido.";
    if ($mileage < 0) $errors[] = "Il chilometraggio non può essere negativo.";
    if ($price <= 0) $errors[] = "Il prezzo deve essere maggiore di zero.";
    
    // Se non ci sono errori, procedi con l'inserimento
    if (empty($errors)) {
        try {
            // Inizia la transazione
            $conn->begin_transaction();
            
            // 2. Inserisci dati nella tabella cars
            $carSql = "INSERT INTO cars (brand, model, year, color, mileage, price, description) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $carStmt = $conn->prepare($carSql);
            $carStmt->bind_param("ssissds", $brand, $model, $year, $color, $mileage, $price, $description);
            $carStmt->execute();
            
            // Ottieni l'ID dell'auto appena inserita
            $carId = $conn->insert_id;
            
            // 3. Inserisci dati nella tabella car_details
            $detailsSql = "INSERT INTO car_details (car_id, transmission, fuel_type) 
                          VALUES (?, ?, ?)";
            $detailsStmt = $conn->prepare($detailsSql);
            $detailsStmt->bind_param("iss", $carId, $transmission, $fuel_type);
            $detailsStmt->execute();
            
            // 4. Inserisci le categorie selezionate
            if (!empty($selectedCategories)) {
                $categorySql = "INSERT INTO car_categories (car_id, category_id) VALUES (?, ?)";
                $categoryStmt = $conn->prepare($categorySql);
                
                foreach ($selectedCategories as $categoryId) {
                    $categoryStmt->bind_param("ii", $carId, $categoryId);
                    $categoryStmt->execute();
                }
            }
            
            // 5. Gestisci il caricamento delle immagini
            if (!empty($_FILES['images']['name'][0])) {
                // Assicurati che la directory per gli upload esista
                $uploadDir = '../../uploads/cars/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Inserisci ogni immagine
                $imageSql = "INSERT INTO car_images (car_id, image_path, is_main) VALUES (?, ?, ?)";
                $imageStmt = $conn->prepare($imageSql);
                
                // Imposta la prima immagine come principale
                $isMainSet = false;
                
                for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
                    // Se il file è stato caricato correttamente
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES['images']['tmp_name'][$i];
                        $fileName = generateUniqueFilename($_FILES['images']['name'][$i]);
                        $uploadPath = $uploadDir . $fileName;
                        
                        // Sposta il file dalla directory temporanea alla destinazione
                        if (move_uploaded_file($tmpName, $uploadPath)) {
                            // Imposta la prima immagine come principale
                            $isMain = !$isMainSet ? 1 : 0;
                            $isMainSet = true;
                            
                            $imageStmt->bind_param("isi", $carId, $fileName, $isMain);
                            $imageStmt->execute();
                        }
                    }
                }
            }
            
            // Commit della transazione
            $conn->commit();
            
            $success = true;
            
            // Reindirizza alla lista auto dopo aver aggiunto con successo
            header('Location: index.php?success=Auto aggiunta con successo');
            exit;
            
        } catch (Exception $e) {
            // Rollback in caso di errore
            $conn->rollback();
            $errors[] = "Errore durante il salvataggio: " . $e->getMessage();
        }
    }
}

$pageTitle = "Aggiungi Auto - Tyson Autogarage";
$additionalCss = "/styles/admin.css";
include '../../includes/header.php';
?>

<div class="admin-container">
    <div class="content">
        <div class="admin-header">
            <h1 class="admin-title">Aggiungi Auto</h1>
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
                Auto aggiunta con successo!
            </div>
        <?php endif; ?>
        
        <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="brand">Marca *</label>
                    <input type="text" id="brand" name="brand" value="<?php echo $_POST['brand'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="model">Modello *</label>
                    <input type="text" id="model" name="model" value="<?php echo $_POST['model'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Anno *</label>
                    <input type="number" id="year" name="year" value="<?php echo $_POST['year'] ?? date('Y'); ?>" min="1900" max="<?php echo date('Y') + 1; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="color">Colore</label>
                    <input type="text" id="color" name="color" value="<?php echo $_POST['color'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="mileage">Chilometraggio *</label>
                    <input type="number" id="mileage" name="mileage" value="<?php echo $_POST['mileage'] ?? ''; ?>" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Prezzo (€) *</label>
                    <input type="text" id="price" name="price" value="<?php echo $_POST['price'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="transmission">Trasmissione</label>
                    <select id="transmission" name="transmission">
                        <option value="">Seleziona...</option>
                        <option value="manual" <?php echo (isset($_POST['transmission']) && $_POST['transmission'] === 'manual') ? 'selected' : ''; ?>>Manuale</option>
                        <option value="automatic" <?php echo (isset($_POST['transmission']) && $_POST['transmission'] === 'automatic') ? 'selected' : ''; ?>>Automatico</option>
                        <option value="semi-automatic" <?php echo (isset($_POST['transmission']) && $_POST['transmission'] === 'semi-automatic') ? 'selected' : ''; ?>>Semi-automatico</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="fuel_type">Carburante</label>
                    <select id="fuel_type" name="fuel_type">
                        <option value="">Seleziona...</option>
                        <option value="petrol" <?php echo (isset($_POST['fuel_type']) && $_POST['fuel_type'] === 'petrol') ? 'selected' : ''; ?>>Benzina</option>
                        <option value="diesel" <?php echo (isset($_POST['fuel_type']) && $_POST['fuel_type'] === 'diesel') ? 'selected' : ''; ?>>Diesel</option>
                        <option value="electric" <?php echo (isset($_POST['fuel_type']) && $_POST['fuel_type'] === 'electric') ? 'selected' : ''; ?>>Elettrico</option>
                        <option value="hybrid" <?php echo (isset($_POST['fuel_type']) && $_POST['fuel_type'] === 'hybrid') ? 'selected' : ''; ?>>Ibrido</option>
                        <option value="lpg" <?php echo (isset($_POST['fuel_type']) && $_POST['fuel_type'] === 'lpg') ? 'selected' : ''; ?>>GPL</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Descrizione</label>
                <textarea id="description" name="description" rows="6"><?php echo $_POST['description'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Categorie</label>
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-checkbox">
                            <input type="checkbox" id="category_<?php echo $category['id']; ?>" name="categories[]" value="<?php echo $category['id']; ?>"
                                <?php echo (isset($_POST['categories']) && in_array($category['id'], $_POST['categories'])) ? 'checked' : ''; ?>>
                            <label for="category_<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="images">Immagini</label>
                <div class="image-upload-container">
                    <div class="image-preview-container" id="imagePreviewContainer"></div>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple class="image-upload">
                    <label for="images" class="image-upload-label">
                        <i class="fas fa-upload"></i> Seleziona Immagini
                    </label>
                    <div class="image-upload-instructions">
                        <p>La prima immagine caricata sarà l'immagine principale. Puoi selezionare più file contemporaneamente.</p>
                        <p>Formati supportati: JPG, PNG, GIF</p>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="button primary">Salva Auto</button>
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
            
            // Imposta la prima immagine come principale
            if (i === 0) {
                previewDiv.classList.add('main-image');
                
                const mainBadge = document.createElement('div');
                mainBadge.className = 'main-badge';
                mainBadge.textContent = 'Principale';
                previewDiv.appendChild(mainBadge);
            }
            
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