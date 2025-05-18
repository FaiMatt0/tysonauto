<?php
// Includi file di configurazione e funzioni
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Verifica login
requireLogin();

// Gestisci paginazione
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Query per contare il totale delle auto
$totalQuery = "SELECT COUNT(*) as total FROM cars";
$totalResult = $conn->query($totalQuery);
$totalCars = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalCars / $perPage);

// Query per ottenere le auto con paginazione
$query = "SELECT c.id, c.brand, c.model, c.year, c.price, c.mileage,
           (SELECT image_path FROM car_images WHERE car_id = c.id AND is_main = 1 LIMIT 1) as main_image
           FROM cars c
           ORDER BY c.id DESC
           LIMIT $perPage OFFSET $offset";
$result = $conn->query($query);
$cars = [];

while ($row = $result->fetch_assoc()) {
    // Se non c'è un'immagine principale, cerca la prima immagine disponibile
    if (empty($row['main_image'])) {
        $imgQuery = "SELECT image_path FROM car_images WHERE car_id = ? LIMIT 1";
        $stmt = $conn->prepare($imgQuery);
        $stmt->bind_param("i", $row['id']);
        $stmt->execute();
        $imgResult = $stmt->get_result();
        if ($imgRow = $imgResult->fetch_assoc()) {
            $row['main_image'] = $imgRow['image_path'];
        }
    }
    
    $cars[] = $row;
}

$pageTitle = "Gestione Auto - Tyson Autogarage";
$additionalCss = "/styles/admin.css";
include '../../includes/header.php';
?>

<div class="admin-container">
    <div class="content">
        <div class="admin-header">
            <h1 class="admin-title">Gestione Auto</h1>
            <a href="/admin/cars/add.php" class="button primary add-button">
                <i class="fas fa-plus"></i> Aggiungi Auto
            </a>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <div class="cars-grid admin-cars">
            <?php if (empty($cars)): ?>
                <div class="no-results">Nessun veicolo trovato. <a href="/admin/cars/add.php">Aggiungi la tua prima auto</a>.</div>
            <?php else: ?>
                <?php foreach ($cars as $car): ?>
                    <div class="car-item">
                        <div class="car-image">
                            <?php if (!empty($car['main_image'])): ?>
                                <img src="/uploads/cars/<?php echo $car['main_image']; ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                            <?php else: ?>
                                <div class="no-image"><i class="fas fa-image"></i> Nessuna immagine</div>
                            <?php endif; ?>
                        </div>
                        <div class="car-info">
                            <h3><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' ' . $car['year']); ?></h3>
                            <div class="car-details">
                                <span class="car-price"><?php echo formatPrice($car['price']); ?></span>
                                <span class="car-mileage"><?php echo number_format($car['mileage'], 0, ',', '.'); ?> km</span>
                            </div>
                        </div>
                        <div class="car-actions">
                            <a href="/admin/cars/edit.php?id=<?php echo $car['id']; ?>" class="action-btn edit-btn" title="Modifica">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/admin/cars/delete.php?id=<?php echo $car['id']; ?>" class="action-btn delete-btn" 
                               title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo veicolo?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="pagination-item">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="pagination-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="pagination-item">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>