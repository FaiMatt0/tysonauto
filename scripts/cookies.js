// Cookie consent functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check if user has already made a cookie choice
    checkCookieConsent();

    // Set up cookie consent handlers
    document.getElementById('accept-cookies').addEventListener('click', acceptCookies);
    document.getElementById('decline-cookies').addEventListener('click', declineCookies);
});

function acceptCookies() {
    // Set cookie consent to accepted
    localStorage.setItem('cookieConsent', 'accepted');
    hideCookieBanner();
    // Here you would initialize any tracking or analytics that require consent
}

function declineCookies() {
    // Set cookie consent to declined
    localStorage.setItem('cookieConsent', 'declined');
    hideCookieBanner();
    // Here you would ensure no tracking cookies are used
}

function checkCookieConsent() {
    const consent = localStorage.getItem('cookieConsent');
    if (consent === 'accepted' || consent === 'declined') {
        hideCookieBanner();
    } else {
        showCookieBanner();
    }
}

function showCookieBanner() {
    const banner = document.getElementById('cookie-consent');
    banner.style.display = 'flex';
}

function hideCookieBanner() {
    const banner = document.getElementById('cookie-consent');
    banner.style.display = 'none';
}
