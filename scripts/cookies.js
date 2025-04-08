// Cookie consent functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check if user has already made a cookie choice
    const cookieConsent = document.getElementById('cookie-consent');
    const acceptButton = document.getElementById('accept-cookies');
    const declineButton = document.getElementById('decline-cookies');
    const cookieChoice = localStorage.getItem('cookieChoice');
    
    // Display cookie consent if no choice has been made
    if (!cookieChoice && cookieConsent) {
        // Delay showing the cookie banner to avoid initial conflicts with language switcher
        setTimeout(() => {
            cookieConsent.classList.add('active');
        }, 1500);
    }
    
    // Safari-specific fix to prevent cookie banner from intercepting language button clicks
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    if (isSafari && cookieConsent) {
        // Ensure language buttons stay clickable by adding specific event handler
        const languageButtons = document.querySelectorAll('.language-selector button, .mobile-language-selector button');
        languageButtons.forEach(button => {
            button.style.zIndex = '10000'; // Higher than cookie banner
            button.style.position = 'relative'; // Ensure z-index works
        });
    }
    
    // Handle accept and decline button clicks
    if (acceptButton) {
        acceptButton.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            localStorage.setItem('cookieChoice', 'accepted');
            cookieConsent.classList.remove('active');
            // Hide the banner completely after animation
            setTimeout(() => {
                cookieConsent.style.display = 'none';
            }, 500);
        });
    }
    
    if (declineButton) {
        declineButton.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            localStorage.setItem('cookieChoice', 'declined');
            cookieConsent.classList.remove('active');
            // Hide the banner completely after animation
            setTimeout(() => {
                cookieConsent.style.display = 'none';
            }, 500);
        });
    }
});
