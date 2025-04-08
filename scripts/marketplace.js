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

// Car gallery functionality
document.addEventListener('DOMContentLoaded', function() {
    const carCards = document.querySelectorAll('.car-card');
    
    carCards.forEach(card => {
        const prevBtn = card.querySelector('.gallery-prev');
        const nextBtn = card.querySelector('.gallery-next');
        const dots = card.querySelectorAll('.gallery-dots .dot');
        const images = card.querySelectorAll('.gallery-container img');
        
        // Set up click handlers for navigation
        if(prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => navigateGallery(card, 'prev'));
            nextBtn.addEventListener('click', () => navigateGallery(card, 'next'));
        }
        
        // Set up click handlers for dots
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                showSlide(card, parseInt(index));
            });
        });
    });
    
    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-button');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter cards
            carCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});

// Navigate gallery
function navigateGallery(card, direction) {
    const images = card.querySelectorAll('.gallery-container img');
    const dots = card.querySelectorAll('.gallery-dots .dot');
    
    let activeIndex = 0;
    images.forEach((img, index) => {
        if (img.classList.contains('active-image')) {
            activeIndex = index;
        }
    });
    
    // Calculate new index
    let newIndex;
    if (direction === 'prev') {
        newIndex = activeIndex === 0 ? images.length - 1 : activeIndex - 1;
    } else {
        newIndex = activeIndex === images.length - 1 ? 0 : activeIndex + 1;
    }
    
    showSlide(card, newIndex);
}

// Show specific slide
function showSlide(card, index) {
    const images = card.querySelectorAll('.gallery-container img');
    const dots = card.querySelectorAll('.gallery-dots .dot');
    
    // Hide all images and deactivate all dots
    images.forEach(img => img.classList.remove('active-image'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    // Show selected image and activate dot
    if (images[index]) images[index].classList.add('active-image');
    if (dots[index]) dots[index].classList.add('active');
}