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

// Services filtering
document.addEventListener('DOMContentLoaded', function() {
    const categoryTabs = document.querySelectorAll('.category-tab');
    const serviceItems = document.querySelectorAll('.service-item');
    
    // Add click event to category tabs
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active tab
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide service items based on category
            if (category === 'all') {
                serviceItems.forEach(item => item.style.display = 'flex');
            } else {
                serviceItems.forEach(item => {
                    if (item.getAttribute('data-category') === category) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
        });
    });
    
    // FAQ accordion functionality
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
            // Toggle current FAQ item
            item.classList.toggle('active');
            
            // Close other FAQ items
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                }
            });
        });
    });
});