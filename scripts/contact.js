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
    
    // Update select options visibility based on language
    updateSelectOptions(lang);
}

// Function to update select options based on language
function updateSelectOptions(lang) {
    const selects = document.querySelectorAll('select');
    selects.forEach(select => {
        const options = select.querySelectorAll('option');
        options.forEach(option => {
            if (option.classList.contains('it-content') || option.classList.contains('en-content')) {
                option.style.display = 'none';
                if (option.classList.contains(lang + '-content')) {
                    option.style.display = 'block';
                }
            }
        });
    });
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

// Form submission
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        subject: document.getElementById('subject').value,
        message: document.getElementById('message').value,
        privacy: document.getElementById('privacy').checked
    };
    
    // Simple validation
    if (!formData.name || !formData.email || !formData.subject || !formData.message || !formData.privacy) {
        showFormStatus('error', 
            savedLanguage === 'it' 
                ? 'Per favore completa tutti i campi obbligatori.' 
                : 'Please complete all required fields.'
        );
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        showFormStatus('error', 
            savedLanguage === 'it' 
                ? 'Per favore inserisci un indirizzo email valido.' 
                : 'Please enter a valid email address.'
        );
        return;
    }
    
    // Here you would typically send the data to your server
    // For demo purposes, we'll just show a success message
    console.log('Form submission:', formData);
    
    // Show success message
    showFormStatus('success', 
        savedLanguage === 'it' 
            ? 'Grazie per il tuo messaggio! Ti risponderemo al più presto.' 
            : 'Thank you for your message! We will respond as soon as possible.'
    );
    
    // Clear form
    this.reset();
});

// Function to show form status
function showFormStatus(type, message) {
    const statusElement = document.getElementById('form-status');
    statusElement.textContent = message;
    statusElement.className = 'form-status ' + type;
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        statusElement.textContent = '';
        statusElement.className = 'form-status';
    }, 5000);
}

// Add CSS for form status
const style = document.createElement('style');
style.textContent = `
    .form-status {
        margin: 15px 0;
        padding: 10px;
        border-radius: 4px;
        text-align: center;
        display: none;
    }
    
    .form-status.success {
        display: block;
        background-color: rgba(76, 175, 80, 0.2);
        color: #2e7d32;
        border: 1px solid #2e7d32;
    }
    
    .form-status.error {
        display: block;
        background-color: rgba(244, 67, 54, 0.2);
        color: #c62828;
        border: 1px solid #c62828;
    }
`;
document.head.appendChild(style);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if select elements exist and update their options
    if (document.querySelector('select')) {
        updateSelectOptions(savedLanguage);
    }
});