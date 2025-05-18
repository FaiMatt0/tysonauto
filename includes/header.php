<?php
// Se la sessione non è già attiva, avviala
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include funzioni di autenticazione se non già incluse
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/auth.php';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/style.css">
    <link rel="stylesheet" href="/styles/mobile.css" media="(max-width: 768px)">
    <?php if (isset($additionalCss)): ?>
        <link rel="stylesheet" href="<?php echo $additionalCss; ?>">
    <?php endif; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="/images/title-logo.png" type="image/x-icon">
    <title><?php echo $pageTitle ?? 'Tyson Autogarage'; ?></title>
</head>
<body>
    <div class="header">
        <div class="content">
            <div class="logo">
                <a href="/index.html">
                    <img src="/images/logo.png" alt="Tyson Autogarage Logo">
                </a>
            </div>
            <nav class="navigation">
                <ul>
                    <li><a href="/index.html">Home</a></li>
                    <li><a href="/pages/about.html">
                        <span class="it-content">Chi Siamo</span>
                        <span class="en-content">About Us</span>
                    </a></li>
                    <li><a href="/pages/services.html">
                        <span class="it-content">Servizi</span>
                        <span class="en-content">Services</span>
                    </a></li>
                    <li><a href="/pages/marketplace.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'marketplace.php') ? 'class="active"' : ''; ?>>Marketplace</a></li>
                    <li><a href="/pages/contact.html">
                        <span class="it-content">Contatti</span>
                        <span class="en-content">Contact</span>
                    </a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="/admin/dashboard.php">
                            <span class="it-content">Dashboard</span>
                            <span class="en-content">Dashboard</span>
                        </a></li>
                        <li><a href="/admin/logout.php">
                            <span class="it-content">Logout</span>
                            <span class="en-content">Logout</span>
                        </a></li>
                    <?php else: ?>
                        <li><a href="/pages/login.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'class="active"' : ''; ?>>
                            <span class="it-content">Accedi</span>
                            <span class="en-content">Login</span>
                        </a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="language-selector">
                <button class="active" data-lang="it">IT</button>
                <button data-lang="en">EN</button>
            </div>
            <div class="mobile-menu-toggle">
                <button class="menu" onclick="this.classList.toggle('opened');this.setAttribute('aria-expanded', this.classList.contains('opened'))" aria-label="Main Menu">
                  <svg width="100" height="100" viewBox="0 0 100 100">
                    <path class="line line1" d="M 20,29.000046 H 80.000231 C 80.000231,29.000046 94.498839,28.817352 94.532987,66.711331 94.543142,77.980673 90.966081,81.670246 85.259173,81.668997 79.552261,81.667751 75.000211,74.999942 75.000211,74.999942 L 25.000021,25.000058" />
                    <path class="line line2" d="M 20,50 H 80" />
                    <path class="line line3" d="M 20,70.999954 H 80.000231 C 80.000231,70.999954 94.498839,71.182648 94.532987,33.288669 94.543142,22.019327 90.966081,18.329754 85.259173,18.331003 79.552261,18.332249 75.000211,25.000058 75.000211,25.000058 L 25.000021,74.999942" />
                  </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="mobile-menu">
        <ul>
            <li><a href="/index.html">Home</a></li>
            <li><a href="/pages/about.html">
                <span class="it-content">Chi Siamo</span>
                <span class="en-content">About Us</span>
            </a></li>
            <li><a href="/pages/services.html">
                <span class="it-content">Servizi</span>
                <span class="en-content">Services</span>
            </a></li>
            <li><a href="/pages/marketplace.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'marketplace.php') ? 'class="active"' : ''; ?>>Marketplace</a></li>
            <li><a href="/pages/contact.html">
                <span class="it-content">Contatti</span>
                <span class="en-content">Contact</span>
            </a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="/admin/dashboard.php">
                    <span class="it-content">Dashboard</span>
                    <span class="en-content">Dashboard</span>
                </a></li>
                <li><a href="/admin/logout.php">
                    <span class="it-content">Logout</span>
                    <span class="en-content">Logout</span>
                </a></li>
            <?php else: ?>
                <li><a href="/pages/login.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'class="active"' : ''; ?>>
                    <span class="it-content">Accedi</span>
                    <span class="en-content">Login</span>
                </a></li>
            <?php endif; ?>
        </ul>
        <div class="mobile-language-selector">
            <button class="active" data-lang="it">IT</button>
            <button data-lang="en">EN</button>
        </div>
    </div>