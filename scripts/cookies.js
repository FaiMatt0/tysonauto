// Cookie consent functionality
document.addEventListener('DOMContentLoaded', function() {
    // Cookie consent
    const cookieConsent = document.getElementById('cookie-consent');
    const acceptButton = document.getElementById('accept-cookies');
    const declineButton = document.getElementById('decline-cookies');
    
    // Check if user has already made a choice
    const cookieChoice = localStorage.getItem('cookieConsent');
    
    // If no choice has been made, show the banner after a delay
    if (!cookieChoice) {
        // Different delay for mobile vs desktop
        const isMobile = window.innerWidth <= 768;
        const delay = isMobile ? 4000 : 2000; // Longer delay for mobile to allow page to load
        
        setTimeout(() => {
            cookieConsent.classList.add('active');
        }, delay);
    }
    
    // Handle accept button click with better touch feedback
    acceptButton.addEventListener('click', function(e) {
        e.preventDefault();
        this.classList.add('clicked');
        
        setTimeout(() => {
            localStorage.setItem('cookieConsent', 'accepted');
            cookieConsent.classList.remove('active');
            console.log('Cookies accepted');
        }, 200);
    });
    
    // Handle decline button click with better touch feedback
    declineButton.addEventListener('click', function(e) {
        e.preventDefault();
        this.classList.add('clicked');
        
        setTimeout(() => {
            localStorage.setItem('cookieConsent', 'declined');
            cookieConsent.classList.remove('active');
            console.log('Cookies declined');
        }, 200);
    });
});
