
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

        // Category tabs
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.category-tab').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                const category = this.getAttribute('data-category');
                
                // Show/hide service items based on category
                document.querySelectorAll('.service-item').forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // FAQ accordions
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', function() {
                const item = this.parentElement;
                item.classList.toggle('active');
            });
        });