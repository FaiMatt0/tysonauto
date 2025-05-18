<?php
// Includi file di configurazione e funzioni
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Verifica login
requireLogin();

// Ottieni statistiche
$totalCars = $conn->query("SELECT COUNT(*) as total FROM cars")->fetch_assoc()['total'];
$totalCategories = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
$totalImages = $conn->query("SELECT COUNT(*) as total FROM car_images")->fetch_assoc()['total'];

// Ottieni ultimi 5 veicoli aggiunti
$latestCars = [];
$result = $conn->query("SELECT id, brand, model, year, price FROM cars ORDER BY id DESC LIMIT 5");
while ($row = $result->fetch_assoc()) {
    $latestCars[] = $row;
}

$pageTitle = "Dashboard - Tyson Autogarage";
$additionalCss = "/styles/admin.css";
include '../includes/header.php';
?>

<div class="admin-container">
    <div class="content">
        <h1 class="admin-title">Dashboard Admin</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-car"></i></div>
                <div class="stat-content">
                    <h3>Veicoli</h3>
                    <div class="stat-value"><?php echo $totalCars; ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tags"></i></div>
                <div class="stat-content">
                    <h3>Categorie</h3>
                    <div class="stat-value"><?php echo $totalCategories; ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-images"></i></div>
                <div class="stat-content">
                    <h3>Immagini</h3>
                    <div class="stat-value"><?php echo $totalImages; ?></div>
                </div>
            </div>
        </div>
        
        <div class="admin-section">
            <div class="section-header">
                <h2>Azioni Rapide</h2>
            </div>
            <div class="actions-grid">
                <a href="/admin/cars/add.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-plus-circle"></i></div>
                    <div class="action-text">Aggiungi Auto</div>
                </a>
                
                <a href="/admin/cars/index.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-list"></i></div>
                    <div class="action-text">Gestisci Auto</div>
                </a>
                
                <a href="/pages/marketplace.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-eye"></i></div>
                    <div class="action-text">Vedi Marketplace</div>
                </a>
            </div>
        </div>
        
        <div class="admin-section">
            <div class="section-header">
                <h2>Ultimi Veicoli Aggiunti</h2>
            </div>
            <div class="latest-cars">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Marca</th>
                            <th>Modello</th>
                            <th>Anno</th>
                            <th>Prezzo</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestCars)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Nessun veicolo trovato</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($latestCars as $car): ?>
                                <tr>
                                    <td><?php echo $car['id']; ?></td>
                                    <td><?php echo htmlspecialchars($car['brand']); ?></td>
                                    <td><?php echo htmlspecialchars($car['model']); ?></td>
                                    <td><?php echo $car['year']; ?></td>
                                    <td><?php echo formatPrice($car['price']); ?></td>
                                    <td class="actions">
                                        <a href="/admin/cars/edit.php?id=<?php echo $car['id']; ?>" class="action-btn edit-btn" title="Modifica">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/admin/cars/delete.php?id=<?php echo $car['id']; ?>" class="action-btn delete-btn" 
                                           title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo veicolo?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>