// ...existing code...

document.addEventListener('DOMContentLoaded', function() {
    // Get language selectors
    const languageSelectors = document.querySelectorAll('.language-selector');
    
    if (languageSelectors) {
        languageSelectors.forEach(selector => {
            selector.addEventListener('click', function(e) {
                e.preventDefault();
                const lang = this.getAttribute('data-lang');
                
                // Set language cookie
                document.cookie = `language=${lang}; path=/; max-age=31536000`;
                
                // For mobile menu, add a small delay before reload to allow menu closing animation
                if (window.innerWidth < 992 || document.querySelector('.mobile-menu-open')) {
                    // Log for debugging
                    console.log('Mobile language change detected');
                    
                    // Prevent default menu close behavior if possible
                    e.stopPropagation();
                    
                    // Set a flag in sessionStorage to track language change
                    sessionStorage.setItem('languageChanged', 'true');
                    sessionStorage.setItem('selectedLanguage', lang);
                    
                    // Add delay to ensure the language change happens after menu animations
                    setTimeout(() => {
                        window.location.reload();
                    }, 300);
                } else {
                    // Desktop behavior - immediate reload
                    window.location.reload();
                }
            });
        });
    }
    
    // Check if we have a pending language change from mobile menu
    if (sessionStorage.getItem('languageChanged') === 'true') {
        console.log('Applying pending language change');
        sessionStorage.removeItem('languageChanged');
        // No need to reload as we're already on a new page load
    }
    
    // Ensure language functionality works after cookie acceptance
    const cookieAcceptButton = document.querySelector('.cookie-accept-btn');
    if (cookieAcceptButton) {
        cookieAcceptButton.addEventListener('click', function() {
            // Re-initialize language selectors after cookie acceptance
            setTimeout(() => {
                const languageSelectors = document.querySelectorAll('.language-selector');
                if (languageSelectors) {
                    languageSelectors.forEach(selector => {
                        // Clone and replace to ensure event handlers are fresh
                        const newSelector = selector.cloneNode(true);
                        newSelector.addEventListener('click', function(e) {
                            e.preventDefault();
                            const lang = this.getAttribute('data-lang');
                            document.cookie = `language=${lang}; path=/; max-age=31536000`;
                            
                            // Apply the same mobile-specific handling
                            if (window.innerWidth < 992 || document.querySelector('.mobile-menu-open')) {
                                e.stopPropagation();
                                sessionStorage.setItem('languageChanged', 'true');
                                sessionStorage.setItem('selectedLanguage', lang);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 300);
                            } else {
                                window.location.reload();
                            }
                        });
                        selector.parentNode.replaceChild(newSelector, selector);
                    });
                }
            }, 100); // Small delay to ensure cookie modal is fully processed
        });
    }
});

// ...existing code...
