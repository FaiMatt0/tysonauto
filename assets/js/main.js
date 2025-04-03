// ...existing code...

// Fix language changer functionality
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
                
                // Reload page to apply language change
                window.location.reload();
            });
        });
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
                            window.location.reload();
                        });
                        selector.parentNode.replaceChild(newSelector, selector);
                    });
                }
            }, 100); // Small delay to ensure cookie modal is fully processed
        });
    }
});

// ...existing code...
