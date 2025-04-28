// Enhanced contact form handler that works with both desktop and mobile forms
document.addEventListener('DOMContentLoaded', function() {
    // Get both form elements
    const desktopForm = document.querySelector('.contactMe form');
    const mobileForm = document.getElementById('contactForm');
    
    // Set up event handlers
    if (desktopForm) {
        console.log('Desktop form found, setting up handler');
        desktopForm.addEventListener('submit', handleFormSubmit);
    } else {
        console.log('Desktop form not found');
    }
    
    if (mobileForm) {
        console.log('Mobile form found, setting up handler');
        mobileForm.addEventListener('submit', handleFormSubmit);
    } else {
        console.log('Mobile form not found');
    }
    
    function handleFormSubmit(event) {
        event.preventDefault();
        console.log('Form submission initiated');
        
        // Get form data
        const form = event.target;
        const isDesktopForm = form.closest('.contactMe') !== null;
        let formData = {};
        
        if (isDesktopForm) {
            // Desktop form - Specific fix for the desktop form issue
            // Get the inputs in the exact order they appear in the form
            const allInputs = Array.from(form.querySelectorAll('input, textarea, select'));
            console.log('Found', allInputs.length, 'input elements');
            
            // The exact mapping for the desktop form based on your HTML structure
            // Note: We're now explicitly using array indexes rather than attributes
            // to ensure we get the correct fields in the right order
            const nameInput = form.querySelector('input[placeholder="Il tuo nome"]');
            const subjectSelect = form.querySelector('select#subject');
            const phoneInput = form.querySelector('input[type="tel"]');
            const emailInput = form.querySelector('input[type="email"]');
            const messageInput = form.querySelector('textarea');
            
            // Log the found inputs for debugging
            console.log('Found inputs:');
            console.log('- Name input:', nameInput ? 'Yes' : 'No');
            console.log('- Subject select:', subjectSelect ? 'Yes' : 'No');
            console.log('- Phone input:', phoneInput ? 'Yes' : 'No');
            console.log('- Email input:', emailInput ? 'Yes' : 'No');
            console.log('- Message input:', messageInput ? 'Yes' : 'No');
            
            // Collect form data based on the exact order of inputs
            formData = {
                name: nameInput ? nameInput.value.trim() : '',
                email: emailInput ? emailInput.value.trim() : '',
                phone: phoneInput ? phoneInput.value.trim() : '',
                subject: subjectSelect ? subjectSelect.value : 'informazioni',
                message: messageInput ? messageInput.value.trim() : '',
                privacy: true // Assume consent for desktop form
            };
            
            // IMPORTANT FIX: If email contains just numbers (like a phone number)
            // and message contains what looks like an email, swap them
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^[0-9+\s()-]{5,20}$/;
            
            if (formData.email && phoneRegex.test(formData.email) && 
                formData.message && emailRegex.test(formData.message)) {
                console.log('FIXING DATA: Email field contains phone, message contains email');
                // Save email from message
                const correctEmail = formData.message;
                // Save phone from email field
                const correctPhone = formData.email;
                // Update values
                formData.email = correctEmail;
                formData.phone = correctPhone;
                formData.message = ''; // Clear message since it contained the email
            }
            
            console.log('Desktop form data correctly mapped:', formData);
        } else {
            // Mobile form (structure from mobile.html)
            // Using IDs which are more reliable for the mobile form
            formData = {
                name: document.getElementById('name').value.trim(),
                email: document.getElementById('email').value.trim(), 
                phone: document.getElementById('phone')?.value.trim() || '',
                subject: document.getElementById('subject')?.value || 'informazioni',
                message: document.getElementById('message').value.trim(),
                privacy: document.getElementById('privacy')?.checked || false
            };
            
            // Apply the same smart correction for mobile form
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^[0-9+\s()-]{5,20}$/;
            
            if (formData.email && phoneRegex.test(formData.email) && 
                formData.message && emailRegex.test(formData.message)) {
                console.log('FIXING DATA: Email field contains phone, message contains email');
                const correctEmail = formData.message;
                const correctPhone = formData.email;
                formData.email = correctEmail;
                formData.phone = correctPhone;
                formData.message = '';
            }
            
            console.log('Mobile form data:', formData);
        }
        
        // Form validation
        if (!validateForm(formData, isDesktopForm)) {
            return false;
        }
        
        // Disable submit button and show loading state
        const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('.primary') || form.querySelector('.submit-btn');
        const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
        
        if (submitBtn) {
            submitBtn.disabled = true;
            if (isDesktopForm) {
                submitBtn.innerHTML = 'Invio in corso...';
            } else {
                // For mobile, we need to handle the button which has a more complex structure
                const btnText = submitBtn.querySelector('.btn-text');
                if (btnText) {
                    btnText.innerText = 'Invio in corso...';
                } else {
                    submitBtn.innerHTML = 'Invio in corso...';
                }
            }
        }
        
        // Determine the base URL
        const baseUrl = 'http://localhost:3000';
        
        // First, try the debug endpoint to verify form data is being received
        console.log('Sending to debug endpoint...');
        fetch(`${baseUrl}/debug-form`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Debug endpoint response:', data);
            
            // If debug succeeded, try the actual email endpoint
            console.log('Now sending to email endpoint...');
            return fetch(`${baseUrl}/send-email`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        })
        .then(response => {
            console.log('Email endpoint status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Email endpoint response:', data);
            
            // Show success message based on form type
            if (isDesktopForm) {
                alert('Messaggio inviato con successo!');
                form.reset();
            } else {
                // For mobile form, use the built-in success feedback
                const formSuccess = document.getElementById('formSuccess');
                const formError = document.getElementById('formError');
                
                if (formSuccess) {
                    formSuccess.style.display = 'flex';
                    formError.style.display = 'none';
                    form.reset();
                    
                    // Hide success message after 5 seconds
                    setTimeout(() => {
                        formSuccess.style.display = 'none';
                    }, 5000);
                } else {
                    alert('Messaggio inviato con successo!');
                    form.reset();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            if (isDesktopForm) {
                alert('Errore durante l\'invio: ' + error.message);
            } else {
                // For mobile form, use the built-in error feedback
                const formSuccess = document.getElementById('formSuccess');
                const formError = document.getElementById('formError');
                const errorMessage = document.querySelector('#formError p');
                
                if (formError) {
                    formError.style.display = 'flex';
                    formSuccess.style.display = 'none';
                    
                    if (errorMessage) {
                        errorMessage.textContent = 'Si è verificato un errore: ' + error.message;
                    }
                    
                    // Hide error message after 5 seconds
                    setTimeout(() => {
                        formError.style.display = 'none';
                    }, 5000);
                } else {
                    alert('Errore durante l\'invio: ' + error.message);
                }
            }
        })
        .finally(() => {
            // Re-enable submit button
            if (submitBtn) {
                submitBtn.disabled = false;
                if (isDesktopForm) {
                    submitBtn.innerHTML = originalBtnText;
                } else {
                    // For mobile, we need to handle the button which has a more complex structure
                    const btnText = submitBtn.querySelector('.btn-text');
                    if (btnText) {
                        btnText.innerText = 'Invia Richiesta';
                    } else {
                        submitBtn.innerHTML = originalBtnText;
                    }
                }
            }
        });
    }
    
    function validateForm(formData, isDesktopForm) {
        let isValid = true;
        
        // Clear previous error messages
        if (!isDesktopForm) {
            document.querySelectorAll('.form-error').forEach(element => {
                element.textContent = '';
            });
        }
        
        // Validate name
        if (!formData.name) {
            isValid = false;
            if (!isDesktopForm) {
                document.getElementById('nameError').textContent = 'Il nome è obbligatorio';
            }
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!formData.email) {
            isValid = false;
            if (!isDesktopForm) {
                document.getElementById('emailError').textContent = 'L\'email è obbligatoria';
            }
        } else if (!emailRegex.test(formData.email)) {
            isValid = false;
            if (!isDesktopForm) {
                document.getElementById('emailError').textContent = 'Inserisci un indirizzo email valido';
            }
        }
        
        // We won't validate the message for this fix, since we might be 
        // using the message field to extract the email in some cases
        
        // Validate privacy checkbox (only for mobile form)
        if (!isDesktopForm && !formData.privacy) {
            isValid = false;
            document.getElementById('privacyError').textContent = 'Devi accettare la privacy policy';
        }
        
        // If not valid and desktop form, show alert
        if (!isValid && isDesktopForm) {
            alert('Per favore, compila tutti i campi obbligatori correttamente.');
        }
        
        console.log('Form validation result:', isValid);
        return isValid;
    }
    
    // Function to load company images
    function loadCompanyImages() {
        // Find company images containers for both desktop and mobile
        const desktopCompanyContainer = document.querySelector('.partners-container');
        const mobileCompanyContainer = document.querySelector('.mobile-partners-container');
        
        // List of company images in the images/aziende folder
        const companyImages = [
            'images/aziende/company1.png',
            'images/aziende/company2.png',
            'images/aziende/company3.png',
            'images/aziende/company4.png',
            'images/aziende/company5.png',
            'images/aziende/company6.png'
            // Add more images as needed
        ];
        
        // Function to create company image elements
        function createCompanyElements(container) {
            if (!container) return;
            
            console.log('Setting up company images for container:', container.className);
            
            // Clear existing content
            container.innerHTML = '';
            
            // Add company images
            companyImages.forEach(imageSrc => {
                const companyDiv = document.createElement('div');
                companyDiv.className = 'partner-logo';
                
                const img = document.createElement('img');
                img.src = imageSrc;
                img.alt = 'Partner aziendale';
                img.loading = 'lazy';
                
                companyDiv.appendChild(img);
                container.appendChild(companyDiv);
            });
        }
        
        // Apply to desktop container
        createCompanyElements(desktopCompanyContainer);
        
        // Apply to mobile container
        createCompanyElements(mobileCompanyContainer);
        
        console.log('Company images loaded successfully');
    }
    
    // Load company images when the page loads
    loadCompanyImages();
});