// Mobile menu toggle
document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
    document.querySelector('.mobile-menu').classList.toggle('active');
    document.body.classList.toggle('menu-open');
});

// Language switcher
function setLanguage(lang) {
    document.documentElement.setAttribute('lang', lang);
    document.querySelectorAll('[data-lang]').forEach(button => {
        button.classList.remove('active');
        if (button.getAttribute('data-lang') === lang) {
            button.classList.add('active');
        }
    });

    // Hide all language content
    document.querySelectorAll('.it-content, .en-content').forEach(element => {
        element.style.display = 'none';
    });

    // Show content for selected language
    document.querySelectorAll('.' + lang + '-content').forEach(element => {
        element.style.display = 'block';
    });

    // Store language preference
    localStorage.setItem('language', lang);
}

// Initialize language
const savedLanguage = localStorage.getItem('language') || 'it';
setLanguage(savedLanguage);

// Add event listeners to language buttons
document.querySelectorAll('[data-lang]').forEach(button => {
    button.addEventListener('click', function() {
        setLanguage(this.getAttribute('data-lang'));
    });
});