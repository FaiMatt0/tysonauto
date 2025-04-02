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

// Car filtering
document.querySelectorAll('.filter-button').forEach(button => {
    button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-button').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Get filter value
        const filter = this.getAttribute('data-filter');
        
        // Filter cars
        document.querySelectorAll('.car-card').forEach(car => {
            if (filter === 'all' || car.getAttribute('data-category') === filter) {
                car.style.display = 'block';
            } else {
                car.style.display = 'none';
            }
        });
    });
});

// Gallery functionality
document.querySelectorAll('.car-card').forEach(card => {
    const prevButton = card.querySelector('.gallery-prev');
    const nextButton = card.querySelector('.gallery-next');
    const dots = card.querySelectorAll('.dot');
    const images = card.querySelectorAll('.gallery-container img');
    let currentIndex = 0;
    
    // Function to show image at the specified index
    function showImage(index) {
        // Hide all images
        images.forEach(img => {
            img.classList.remove('active-image');
        });
        
        // Deactivate all dots
        dots.forEach(dot => {
            dot.classList.remove('active');
        });
        
        // Show the selected image and activate the corresponding dot
        images[index].classList.add('active-image');
        dots[index].classList.add('active');
        
        // Update current index
        currentIndex = index;
    }
    
    // Previous button click
    if (prevButton) {
        prevButton.addEventListener('click', function(e) {
            e.preventDefault();
            let newIndex = currentIndex - 1;
            if (newIndex < 0) {
                newIndex = images.length - 1;
            }
            showImage(newIndex);
        });
    }
    
    // Next button click
    if (nextButton) {
        nextButton.addEventListener('click', function(e) {
            e.preventDefault();
            let newIndex = currentIndex + 1;
            if (newIndex >= images.length) {
                newIndex = 0;
            }
            showImage(newIndex);
        });
    }
    
    // Dot navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
            showImage(index);
        });
    });
});

// Pagination (for demonstration purposes)
document.querySelectorAll('.pagination-item').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.pagination-item').forEach(i => {
            i.classList.remove('active');
        });
        this.classList.add('active');
    });
});