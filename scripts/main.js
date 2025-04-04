document.addEventListener('DOMContentLoaded', function() {
    // Set up language switchers
    setupLanguageSwitchers();
    
    // Load testimonials
    loadTestimonials();
    
    // Handle mobile menu
    setupMobileMenu();
    
    // Handle loading screen
    handleLoadingScreen();
});

function setupLanguageSwitchers() {
    // Setup desktop language buttons
    const desktopLangButtons = document.querySelectorAll('.language-selector button');
    desktopLangButtons.forEach(button => {
        button.addEventListener('click', function() {
            changeLanguage(this.getAttribute('data-lang'));
            
            // Update both desktop and mobile UI
            updateLanguageUI(this.getAttribute('data-lang'));
        });
    });

    // Setup mobile language buttons
    const mobileLangButtons = document.querySelectorAll('.mobile-language-selector button');
    mobileLangButtons.forEach(button => {
        button.addEventListener('click', function() {
            changeLanguage(this.getAttribute('data-lang'));
            
            // Update both desktop and mobile UI
            updateLanguageUI(this.getAttribute('data-lang'));
        });
    });

    // Load saved language preference or default to Italian
    const savedLang = localStorage.getItem('language') || 'it';
    changeLanguage(savedLang);
    updateLanguageUI(savedLang);
}

function changeLanguage(lang) {
    // Store the language preference
    localStorage.setItem('language', lang);
    
    // Hide all language content
    document.querySelectorAll('.it-content, .en-content').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show selected language content
    document.querySelectorAll('.' + lang + '-content').forEach(el => {
        el.style.display = 'inline-block';
    });
}

function updateLanguageUI(lang) {
    // Update desktop language buttons
    document.querySelectorAll('.language-selector button').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
    });
    
    // Update mobile language buttons
    document.querySelectorAll('.mobile-language-selector button').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
    });
}

function setupMobileMenu() {
    const menuToggle = document.querySelector('.mobile-menu-toggle button');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    menuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
    });
}

function handleLoadingScreen() {
    setTimeout(() => {
        document.getElementById('loading-screen').style.opacity = '0';
        setTimeout(() => {
            document.getElementById('loading-screen').style.display = 'none';
        }, 500);
    }, 2500);
}

function loadTestimonials() {
    // Testimonials data
    const testimonials = [
        {
            name: 'Marco Rossi',
            comment: {
                it: 'Servizio eccellente! Ho portato la mia auto per una riparazione e sono rimasto impressionato dalla rapidità e professionalità.',
                en: 'Excellent service! I brought my car for repair and was impressed by the speed and professionalism.'
            },
            rating: 5
        },
        {
            name: 'Laura Bianchi',
            comment: {
                it: 'Personale molto competente e prezzi onesti. Consiglio vivamente Tyson Autogarage per qualsiasi problema con la vostra auto.',
                en: 'Very competent staff and honest prices. I highly recommend Tyson Autogarage for any issues with your car.'
            },
            rating: 5
        },
        {
            name: 'Andrea Verdi',
            comment: {
                it: 'Ottima esperienza! Hanno risolto un problema che altre officine non erano riuscite a diagnosticare correttamente.',
                en: 'Great experience! They solved an issue that other workshops had failed to diagnose correctly.'
            },
            rating: 5
        }
    ];
    
    const slider = document.querySelector('.testimonial-slider');
    const pagination = document.querySelector('.testimonial-pagination');
    
    if (slider) {
        testimonials.forEach((testimonial, index) => {
            const slide = document.createElement('div');
            slide.className = 'testimonial';
            slide.innerHTML = `
                <div class="testimonial-content">
                    <div class="testimonial-text">
                        <p class="it-content">${testimonial.comment.it}</p>
                        <p class="en-content">${testimonial.comment.en}</p>
                    </div>
                    <div class="testimonial-author">
                        <h4>${testimonial.name}</h4>
                        <div class="rating">
                            ${Array(5).fill().map((_, i) => `<i class="fas fa-star ${i < testimonial.rating ? 'filled' : ''}"></i>`).join('')}
                        </div>
                    </div>
                </div>
            `;
            slider.appendChild(slide);
            
            // Add pagination dot
            const dot = document.createElement('span');
            dot.className = 'pagination-dot' + (index === 0 ? ' active' : '');
            dot.setAttribute('data-index', index);
            pagination.appendChild(dot);
            
            dot.addEventListener('click', () => {
                showTestimonial(index);
            });
        });
        
        // Setup navigation
        document.querySelector('.testimonial-prev').addEventListener('click', () => {
            navigateTestimonial(-1);
        });
        
        document.querySelector('.testimonial-next').addEventListener('click', () => {
            navigateTestimonial(1);
        });
        
        // Start with first testimonial
        showTestimonial(0);
    }
}

let currentTestimonial = 0;

function showTestimonial(index) {
    const slides = document.querySelectorAll('.testimonial');
    const dots = document.querySelectorAll('.pagination-dot');
    
    if (!slides.length) return;
    
    slides.forEach((slide, i) => {
        slide.style.display = i === index ? 'block' : 'none';
    });
    
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
    });
    
    currentTestimonial = index;
}

function navigateTestimonial(direction) {
    const slides = document.querySelectorAll('.testimonial');
    let newIndex = currentTestimonial + direction;
    
    if (newIndex < 0) newIndex = slides.length - 1;
    if (newIndex >= slides.length) newIndex = 0;
    
    showTestimonial(newIndex);
}