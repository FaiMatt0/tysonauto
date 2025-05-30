<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/mobile.css" media="(max-width: 768px)">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/title-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../styles/login.css">
    <title>Tyson Autogarage - Login</title>
</head>
<body>
    <!-- Background Video -->
    <video class="background-video" autoplay muted loop playsinline>
        <source src="../images/flag.mp4" type="video/mp4">
    </video>
    
    <div class="header">
        <div class="content">
            <div class="logo">
                <a href="../index.html">
                    <img src="../images/logo.png" alt="Tyson Autogarage Logo">
                </a>
            </div>
            <nav class="navigation">
                <ul>
                    <li><a href="../index.html">Home</a></li>
                    <li><a href="about.html">Chi Siamo</a></li>
                    <li><a href="services.html">Servizi</a></li>
                    <li><a href="marketplace.html">Marketplace</a></li>
                    <li><a href="contact.html">Contatti</a></li>
                    <li><a href="login.html" class="active">
                        <span class="it-content">Accedi</span>
                        <span class="en-content">Login</span>
                    </a></li>
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
            <li><a href="../index.html"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="about.html"><i class="fas fa-info-circle"></i> Chi Siamo</a></li>
            <li><a href="services.html"><i class="fas fa-tools"></i> Servizi</a></li>
            <li><a href="marketplace.html"><i class="fas fa-shopping-cart"></i> Marketplace</a></li>
            <li><a href="contact.html"><i class="fas fa-envelope"></i> Contatti</a></li>
            <li><a href="login.html" class="active"><i class="fas fa-user"></i> 
                <span class="it-content">Accedi</span>
                <span class="en-content">Login</span>
            </a></li>
        </ul>
        <div class="mobile-language-selector">
            <button class="active" data-lang="it">IT</button>
            <button data-lang="en">EN</button>
        </div>
    </div>

    <div class="login-section">
        <div class="content">
            <div class="login-container">
                <h2 class="section-title">
                    <span class="it-content">AREA RISERVATA</span>
                    <span class="en-content">RESTRICTED AREA</span>
                </h2>
                
                <form class="login-form" id="login-form">
                    <div class="form-group">
                        <label for="email">
                            <span class="it-content">Email</span>
                            <span class="en-content">Email</span>
                        </label>
                        <input type="email" id="email" name="email" autocomplete="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <span class="it-content">Password</span>
                            <span class="en-content">Password</span>
                        </label>
                        <input type="password" id="password" name="password" autocomplete="current-password" required>
                    </div>
                    
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">
                                <span class="it-content">Ricordami</span>
                                <span class="en-content">Remember me</span>
                            </label>
                        </div>
                        <a href="#" class="forgot-password">
                            <span class="it-content">Password dimenticata?</span>
                            <span class="en-content">Forgot password?</span>
                        </a>
                    </div>
                    
                    <button type="submit" class="button primary login-button">
                        <span class="it-content">Accedi</span>
                        <span class="en-content">Login</span>
                    </button>
                </form>
                
                <!-- Removed the "Non hai un account?" section -->
            </div>
        </div>
    </div>

    <footer>
        <div class="content">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>Tyson Autogarage</h3>
                    <p class="it-content">La tua officina automobilistica di fiducia dal 2020.</p>
                    <p class="en-content">Your trusted auto garage since 2020.</p>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/Tysonautoaviano/"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/tysonauto_garage"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.tiktok.com/@tysonauto1"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3 class="it-content">Collegamenti Rapidi</h3>
                    <h3 class="en-content">Quick Links</h3>
                    <ul>
                        <li><a href="../index.html">Home</a></li>
                        <li><a href="about.html">
                            <span class="it-content">Chi Siamo</span>
                            <span class="en-content">About Us</span>
                        </a></li>
                        <li><a href="services.html">
                            <span class="it-content">Servizi</span>
                            <span class="en-content">Services</span>
                        </a></li>
                        <li><a href="marketplace.html">Marketplace</a></li>
                        <li><a href="contact.html">
                            <span class="it-content">Contatti</span>
                            <span class="en-content">Contact</span>
                        </a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="it-content">Contatti</h3>
                    <h3 class="en-content">Contact</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Via Giuseppe Ellero, 17, Aviano, Italy</p>
                    <p><i class="fas fa-phone"></i> 351 659 2435</p>
                    <p><i class="fas fa-envelope"></i> tysonautoaviano@gmail.com</p>
                </div>
                <div class="footer-column">
                    <h3 class="it-content">Orari di Apertura</h3>
                    <h3 class="en-content">Opening Hours</h3>
                    <p class="it-content">Lunedì - Sabato: 7:00 - 19:00</p>
                    <p class="en-content">Monday - Saturday: 7:00 - 19:00</p>
                    <p class="it-content">Domenica: Chiuso</p>
                    <p class="en-content">Sunday: Closed</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Tyson Autogarage. 
                    <span class="it-content">Tutti i diritti riservati.</span>
                    <span class="en-content">All rights reserved.</span>
                </p>
            </div>
        </div>
    </footer>

    <script src="../scripts/main.js"></script>
</body>
</html>
