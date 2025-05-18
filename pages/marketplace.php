<?php
// Includi file di configurazione e funzioni
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Gestisci filtri e ricerca
$categoryFilter = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 6; // Veicoli per pagina
$offset = ($page - 1) * $perPage;

// Costruisci la query di base
$baseQuery = "
    SELECT c.id, c.brand, c.model, c.year, c.price, c.mileage, c.description, c.color,
           d.transmission, d.fuel_type
    FROM cars c
    LEFT JOIN car_details d ON c.id = d.car_id
";

$countQuery = "SELECT COUNT(*) as total FROM cars c";
$whereConditions = [];
$params = [];
$types = "";

// Aggiungi condizioni di categoria se è selezionata una categoria
if ($categoryFilter !== 'all') {
    $baseQuery .= " JOIN car_categories cc ON c.id = cc.car_id 
                    JOIN categories cat ON cc.category_id = cat.id";
    $countQuery .= " JOIN car_categories cc ON c.id = cc.car_id 
                     JOIN categories cat ON cc.category_id = cat.id";
    $whereConditions[] = "cat.name = ?";
    $params[] = $categoryFilter;
    $types .= "s";
}

// Aggiungi condizioni di ricerca se è presente un termine di ricerca
if (!empty($search)) {
    $whereConditions[] = "(c.brand LIKE ? OR c.model LIKE ? OR c.description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

// Applica le condizioni WHERE se presenti
if (!empty($whereConditions)) {
    $baseQuery .= " WHERE " . implode(" AND ", $whereConditions);
    $countQuery .= " WHERE " . implode(" AND ", $whereConditions);
}

// Completa la query con ordinamento e limite
$query = $baseQuery . " ORDER BY c.id DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $perPage;
$types .= "ii";

// Prepara e esegui la query di conteggio
$countStmt = $conn->prepare($countQuery);
if (!empty($params)) {
    // Rimuovi gli ultimi due parametri che sono per la paginazione
    $countTypes = substr($types, 0, -2);
    $countParams = array_slice($params, 0, -2);
    
    if (!empty($countParams)) {
        // Usa bind_param dinamicamente
        $countBindParams = array();
        $countBindParams[] = &$countTypes;
        for ($i = 0; $i < count($countParams); $i++) {
            $countBindParams[] = &$countParams[$i];
        }
        call_user_func_array(array($countStmt, 'bind_param'), $countBindParams);
    }
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalCars = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalCars / $perPage);

// Prepara e esegui la query principale
$stmt = $conn->prepare($query);
if (!empty($params)) {
    // Usa bind_param dinamicamente
    $bindParams = array();
    $bindParams[] = &$types;
    for ($i = 0; $i < count($params); $i++) {
        $bindParams[] = &$params[$i];
    }
    call_user_func_array(array($stmt, 'bind_param'), $bindParams);
}
$stmt->execute();
$result = $stmt->get_result();

// Ottieni tutte le categorie per i filtri
$categoriesQuery = "SELECT id, name FROM categories ORDER BY name";
$categoriesResult = $conn->query($categoriesQuery);
$categories = [];
while ($category = $categoriesResult->fetch_assoc()) {
    $categories[] = $category;
}

$pageTitle = "Marketplace - Tyson Autogarage";
$additionalCss = "/styles/marketplace.css";
include '../includes/header.php';
?>

<div class="page-header">
    <div class="content">
        <div class="breadcrumbs">
            <a href="/index.html">Home</a> / Marketplace
        </div>
        <h1>Marketplace</h1>
    </div>
</div>

<div class="marketplace-intro">
    <div class="content">
        <h2>
            <span class="it-content">Auto Usate Selezionate</span>
            <span class="en-content">Selected Used Cars</span>
        </h2>
        <p class="it-content">
            Nel nostro marketplace troverai una selezione di auto usate di qualità, 
            accuratamente ispezionate e certificate dai nostri tecnici esperti.
            Ogni veicolo viene sottoposto a controlli rigorosi prima di essere messo in vendita.
        </p>
        <p class="en-content">
            In our marketplace, you'll find a selection of quality used cars, 
            carefully inspected and certified by our expert technicians.
            Each vehicle undergoes rigorous checks before being put up for sale.
        </p>
        
        <?php if (isLoggedIn()): ?>
        <div class="admin-actions">
            <a href="/admin/cars/add.php" class="button primary admin-button">
                <i class="fas fa-plus"></i> 
                <span class="it-content">Aggiungi Auto</span>
                <span class="en-content">Add Car</span>
            </a>
            <a href="/admin/cars/index.php" class="button secondary admin-button">
                <i class="fas fa-cog"></i> 
                <span class="it-content">Gestisci Auto</span>
                <span class="en-content">Manage Cars</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="filter-section">
    <div class="content">
        <div class="filter-container">
            <a href="?category=all" class="filter-button <?php echo $categoryFilter === 'all' ? 'active' : ''; ?>">
                <span class="it-content">Tutti</span>
                <span class="en-content">All</span>
            </a>
            <?php foreach ($categories as $category): ?>
                <a href="?category=<?php echo urlencode($category['name']); ?>" 
                   class="filter-button <?php echo $categoryFilter === $category['name'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <form action="" method="GET" class="search-container">
            <input type="text" name="search" placeholder="Cerca per marca, modello..." value="<?php echo htmlspecialchars($search); ?>">
            <?php if ($categoryFilter !== 'all'): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
            <?php endif; ?>
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </form>

        <div class="cars-grid">
            <?php if ($result->num_rows === 0): ?>
                <div class="no-results">
                    <span class="it-content">Nessun veicolo trovato con i filtri selezionati.</span>
                    <span class="en-content">No vehicles found with the selected filters.</span>
                </div>
            <?php else: ?>
                <?php while ($car = $result->fetch_assoc()): 
                    // Ottieni le immagini per questa auto
                    $carImages = getCarImages($car['id'], $conn);
                    $imageCount = count($carImages);
                    
                    // Ottieni le categorie per questa auto
                    $carCategories = getCarCategories($car['id'], $conn);
                    $categoryNames = array_map(function($cat) {
                        return strtolower(str_replace(' ', '-', $cat['name']));
                    }, $carCategories);
                ?>
                    <div class="car-card" data-category="<?php echo implode(' ', $categoryNames); ?>">
                        <div class="car-image">
                            <div class="gallery-container">
                                <?php if ($imageCount > 0): ?>
                                    <?php 
                                    // Trova l'immagine principale
                                    $mainImage = null;
                                    foreach ($carImages as $image) {
                                        if ($image['is_main']) {
                                            $mainImage = $image;
                                            break;
                                        }
                                    }
                                    
                                    // Se non c'è un'immagine principale, usa la prima
                                    if (!$mainImage && $imageCount > 0) {
                                        $mainImage = $carImages[0];
                                    }
                                    
                                    // Mostra l'immagine principale
                                    if ($mainImage): 
                                    ?>
                                        <img src="/uploads/cars/<?php echo $mainImage['image_path']; ?>" 
                                             alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" 
                                             class="active-image">
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Mostra tutte le altre immagini
                                    foreach ($carImages as $image): 
                                        if ($image !== $mainImage):
                                    ?>
                                        <img src="/uploads/cars/<?php echo $image['image_path']; ?>" 
                                             alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" 
                                             class="gallery-image">
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                <?php else: ?>
                                    <img src="/images/no-image.jpg" alt="Immagine non disponibile" class="active-image">
                                <?php endif; ?>
                            </div>
                            <?php if ($imageCount > 1): ?>
                            <div class="gallery-controls">
                                <button class="gallery-prev"><i class="fas fa-chevron-left"></i></button>
                                <button class="gallery-next"><i class="fas fa-chevron-right"></i></button>
                            </div>
                            <div class="gallery-dots">
                                <?php for ($i = 0; $i < $imageCount; $i++): ?>
                                    <span class="dot <?php echo ($i === 0) ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                                <?php endfor; ?>
                            </div>
                            <?php endif; ?>
                            <div class="car-price"><?php echo formatPrice($car['price']); ?></div>
                        </div>
                        <div class="car-details">
                            <h3 class="car-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' ' . $car['year']); ?></h3>
                            <div class="car-specs">
                                <div class="car-spec">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span><?php echo number_format($car['mileage'], 0, ',', '.'); ?> km</span>
                                </div>
                                <?php if (!empty($car['fuel_type'])): ?>
                                <div class="car-spec">
                                    <i class="fas fa-gas-pump"></i>
                                    <span>
                                        <?php 
                                        $fuelTypes = [
                                            'petrol' => 'Benzina',
                                            'diesel' => 'Diesel',
                                            'electric' => 'Elettrico',
                                            'hybrid' => 'Ibrido',
                                            'lpg' => 'GPL'
                                        ];
                                        echo $fuelTypes[$car['fuel_type']] ?? $car['fuel_type'];
                                        ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($car['transmission'])): ?>
                                <div class="car-spec">
                                    <i class="fas fa-cog"></i>
                                    <span>
                                        <?php 
                                        $transmissionTypes = [
                                            'manual' => 'Manuale',
                                            'automatic' => 'Automatico',
                                            'semi-automatic' => 'Semi-automatico'
                                        ];
                                        echo $transmissionTypes[$car['transmission']] ?? $car['transmission'];
                                        ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <p class="car-description it-content">
                                <?php 
                                // Limita la descrizione a 100 caratteri
                                $desc = htmlspecialchars($car['description'] ?? '');
                                echo (strlen($desc) > 100) ? substr($desc, 0, 97) . '...' : $desc;
                                ?>
                            </p>
                            <p class="car-description en-content">
                                <?php 
                                // Versione inglese (puoi implementare traduzioni in futuro)
                                $desc = htmlspecialchars($car['description'] ?? '');
                                echo (strlen($desc) > 100) ? substr($desc, 0, 97) . '...' : $desc;
                                ?>
                            </p>
                            <div class="car-actions">
                                <a href="/pages/contact.html" class="button secondary">
                                    <span class="it-content">Contatta per Dettagli</span>
                                    <span class="en-content">Contact for Details</span>
                                </a>
                                <?php if (isLoggedIn()): ?>
                                <a href="/admin/cars/edit.php?id=<?php echo $car['id']; ?>" class="button primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-item">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                       class="pagination-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-item">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="cta-section">
    <div class="content">
        <h2>
            <span class="it-content">Interessato a uno dei nostri veicoli?</span>
            <span class="en-content">Interested in one of our vehicles?</span>
        </h2>
        <p class="it-content">Contattaci per maggiori informazioni o per prenotare un test drive.</p>
        <p class="en-content">Contact us for more information or to schedule a test drive.</p>
        <a href="/pages/contact.html" class="button primary">
            <span class="it-content">Contattaci Ora</span>
            <span class="en-content">Contact Us Now</span>
        </a>
    </div>
</div>

<?php 
$additionalJs = "/scripts/marketplace.js";
include '../includes/footer.php'; 
?>