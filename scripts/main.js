// Loading Screen Animation
document.addEventListener('DOMContentLoaded', function() {
    const loadingScreen = document.getElementById('loading-screen');
    const introVideo = document.querySelector('.intro-video');
    
    // Make sure video is playing
    introVideo.play().catch(err => {
        console.log('Auto-play was prevented, please enable it in your browser settings');
    });
    
    // Display the loading animation for a longer time
    setTimeout(function() {
        loadingScreen.classList.add('loading-hidden');
        
        // Remove from DOM after transition completes
        setTimeout(function() {
            loadingScreen.style.display = 'none';
        }, 1200);
    }, 4000); // Show intro for 4 seconds
});

// Loading screen handling
document.addEventListener('DOMContentLoaded', function() {
    const loadingScreen = document.getElementById('loading-screen');
    
    // Shorter intro duration - show loading screen for only 2 seconds
    setTimeout(function() {
        loadingScreen.style.opacity = '0';
        
        // Remove loading screen from DOM after fade out animation completes
        setTimeout(function() {
            loadingScreen.style.display = 'none';
            document.body.classList.add('loaded');
        }, 500); // Wait for fade out animation to complete
        
    }, 2000); // Reduced time from default to 2 seconds
});

// Mobile menu toggle
document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
    document.querySelector('.mobile-menu').classList.toggle('active');
    document.body.classList.toggle('menu-open');
});

// Testimonials data
const testimonials = [
    {
        name: "Zayah Davis",
        text: "Tyson's shop may have a mixed reputation, but there's a reason people from all over base still bring their vehicles here. Look at how full the shop is! I came in to get my tires changed, and despite the chatter, Tyson got me new tires and worked on them as soon as he could. He was upfront about the pricing—no surprises—and took the time to explain everything clearly. While the shop may not be perfect, Tyson's honesty, fair prices, and willingness to help make it worth coming back. If you're willing to give it a shot, you might be pleasantly surprised."
    },
    {
        name: "Alex_g18",
        text: "Great service for great prices. Gave them very short notice for my power steering going out and they got me in the same day. They diagnosed the source of the problem, got parts, and made the needed repairs in less than 5 hours. Best experience I've ever had at an auto shop. Highly recommend."
    },
    {
        name: "Ryan Mathews",
        text: "This place is great! I had a large screw stuck in my tire and they were able to repair it on the spot. I messaged on WhatsApp and they said to bring it in. Luckily it's right around the corner from my house, brought it in and they had me back in service within 15 mins! Entrance is hard to find, but when you drive past you'll know it. Thanks Tysons!"
    },
    {
        name: "Hafiz Rahman",
        text: "Tyson is truly the best of the best when it comes to mechanics. His honest pricing, outstanding workmanship, and unwavering dedication make him stand out among the rest. From the moment I arrived at his shop, Tyson's professionalism and friendly demeanor were evident. He took the time to explain the repairs needed, providing transparent and fair pricing without any hidden fees. His attention to detail and expertise were remarkable, ensuring that every issue was addressed with precision. Despite a busy schedule, Tyson completed the repairs promptly, showcasing his hard work and commitment. If you're seeking top-quality service at an honest price, Tyson is the mechanic to trust."
    },
    {
        name: "Curtin",
        text: "These guys are great. Had a transmission problem and they were able to fix the issue within a few days. Highly recommend them for anything car related."
    },
    {
        name: "Tyan Taylor",
        text: "I've been going to him for years, unbeatable prices, very easy to work with, he painted, tinted, and put new tires on multiple of my cars, and did all mechanic work from oil changes to swapping out transmissions, definitely recommend"
    },
    {
        name: "GABRIELLA USCHNIG",
        text: "Tyson Auto is the place to go, if you need your car taken care of. They are professional, fast, very helpful and - last but not least - very honest. The owner will do everything to accommodate your needs. I was waiting more than a year nowv to get some work done on my car, while other body shops had no time, were too busy for some \"minor\" work AND much too expensive. I wish I'd have known this place earlier. 🤩🤩"
    },
    {
        name: "I. Zay",
        text: "The first time I've came to Tyson Auto to get some work done on my car, he changed the headgasket and had it running like brand new! That was about 3 months ago, and my car still runs flawlessly. I just came back today to get my windows tinted. Not only did he get it done on the same day but quickly and for a great price! I'd highly recommend his shop to anyone looking to get quality maintenance done by a mechanic you can trust!"
    },
    {
        name: "Marta Głowacz",
        text: "I can't recommend Tyson enough! On two separate occasions, they saved me by fixing my car on the spot, quickly and professionally. Thank you again for your excellent service!"
    },
    {
        name: "Jonathan Gutierrez",
        text: "Saw an ad for this shop and decided to take my car in with no appointment scheduled. I told Tyson the possible AC issue and he verified telling me he has the part in stock. Without hesitation he installed the new condenser and my AC has never been colder! Decent pricing, excellent customer service and I'll be sure to bring my car back if need be. Highly recommended!"
    },
    {
        name: "Sarah Savage",
        text: "I have been going to Tyson's for around two years now on multiple occasions. He's friendly and easy to work with. Always takes care of any issues I have. Prices are very fair and will continue to give him my business. Happy customer here"
    },
    {
        name: "Aaron Daniels",
        text: "These is the best auto repair shops in all Aviano. If your military go here very military friendly hospitality is amazing take care of you at a great price very quick own by U.S military as well so definitely come here for sure"
    },
    {
        name: "John Lane",
        text: "Good shop. Good vibes, very very friendly team. I remember I didn't have all the money with me and literally just said \" go home, when ever you have the money, just come and pay me\". You can't go wrong with the team. Dependable. When he says the car will be ready in 3 minutes. It will be ready. Also he does have the best price- hands down."
    },
    {
        name: "Chantry Hendry",
        text: "Tyson did superb with the window tint. Car even got washed after he was done. Best customer service in aviano hands down."
    },
    {
        name: "Amanda Jeong",
        text: "Very fast service and great work. Needed repaint on my car. Got it done in a day."
    },
    {
        name: "Ali M",
        text: "Tyson and his team are awesome in communication, technical skills, and competitive pricing. I recommend this place to those are willing to wait for great results 🤝"
    },
    {
        name: "G 2",
        text: "Heard nothing but good things about Tyson Auto then tried his shop for myself and witnessed first hand that his work speaks for itself! Great attitude, honest, with great attention to detail. He restored my headlights, installed new bulbs for nearly nothing then gave my BMW that luxurious look with all the way around ceramic tint! All done in just one day. Highly recommend!"
    },
    {
        name: "Justin Waruszewski",
        text: "Today I contacted Tyson auto about wanting to get my windows tinted to match the back windows. He got me in same day and I had my car back in just a few hours. He was very kind and eager to help. The job was done well and the only minor flaw that was there he fixed immediately when I saw it. (A tiny bubble). I would recommend getting your windows tinted here!"
    },
    {
        name: "Marcos Palmer",
        text: "Not many shops are able to work or willing to work on my f150….for Tyson no problem! Especially for something as tough as a transmission problem. He found the problem and got it fix within a week! All with fair pricing and customer service being the best in Italy. Couldn't recommend him enough!"
    },
    {
        name: "Steven Rushing",
        text: "They were incredibly kind, service was excellent. They obviously have plenty of work but fit me in for a same-day oil change and charged me half what another local mechanic quoted me. The mechanic and receptionist both spoke perfect English if that is important for you."
    }
];

// Testimonial slider functionality
document.addEventListener('DOMContentLoaded', function() {
    const testimonialSlider = document.querySelector('.testimonial-slider');
    const testimonialPagination = document.querySelector('.testimonial-pagination');
    const prevButton = document.querySelector('.testimonial-prev');
    const nextButton = document.querySelector('.testimonial-next');
    
    // Constants for testimonial display
    const testimonialsPerPage = 4;
    const totalPages = Math.ceil(testimonials.length / testimonialsPerPage);
    let currentPage = 0;
    
    // Initialize testimonials
    function initTestimonials() {
        // Create pagination dots
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('span');
            dot.classList.add('pagination-dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                goToPage(i);
            });
            testimonialPagination.appendChild(dot);
        }
        
        // Display first page
        displayTestimonials(0);
        
        // Add event listeners to navigation buttons
        prevButton.addEventListener('click', prevPage);
        nextButton.addEventListener('click', nextPage);
    }
    
    // Display testimonials for the given page
    function displayTestimonials(page) {
        // Clear current testimonials
        testimonialSlider.innerHTML = '';
        
        // Calculate start and end index for current page
        const startIndex = page * testimonialsPerPage;
        const endIndex = Math.min(startIndex + testimonialsPerPage, testimonials.length);
        
        // Create and append testimonial cards
        for (let i = startIndex; i < endIndex; i++) {
            const testimonial = testimonials[i];
            
            const testimonialCard = document.createElement('div');
            testimonialCard.classList.add('testimonial-card');
            
            testimonialCard.innerHTML = `
                <div class="testimonial-content">
                    <div class="header-row">
                        <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p>${testimonial.text}</p>
                    <div class="testimonial-footer">
                        <div class="google-logo">
                            <img src="images/google-logo.png" alt="Google Review">
                        </div>
                        <div class="testimonial-author">- ${testimonial.name}</div>
                    </div>
                </div>
            `;
            
            testimonialSlider.appendChild(testimonialCard);
        }
        
        // Update pagination dots
        updatePagination(page);
    }
    
    // Update pagination dots to highlight the current page
    function updatePagination(page) {
        const dots = testimonialPagination.querySelectorAll('.pagination-dot');
        dots.forEach((dot, index) => {
            if (index === page) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
    
    // Go to previous page
    function prevPage() {
        if (currentPage > 0) {
            goToPage(currentPage - 1);
        }
    }
    
    // Go to next page
    function nextPage() {
        if (currentPage < totalPages - 1) {
            goToPage(currentPage + 1);
        }
    }
    
    // Go to a specific page
    function goToPage(page) {
        currentPage = page;
        displayTestimonials(page);
    }
    
    // Initialize the testimonials if they exist on the page
    if (testimonialSlider) {
        initTestimonials();
    }
});

// Language switcher functionality
document.addEventListener('DOMContentLoaded', function() {
    // Select all language buttons (both in desktop and mobile menus)
    const languageButtons = document.querySelectorAll('.language-selector button, .mobile-language-selector button');
    
    // Get saved language preference or default to Italian
    const savedLanguage = localStorage.getItem('language') || 'it';
    
    // Detect Safari browser
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    
    // Function to switch language
    function switchLanguage(language) {
        // Save preference to localStorage
        localStorage.setItem('language', language);
        
        // Apply language-specific classes instead of directly manipulating display property
        document.body.classList.remove('lang-it', 'lang-en');
        document.body.classList.add('lang-' + language);
        
        // Special handling for cookie consent banner
        const cookieConsent = document.getElementById('cookie-consent');
        if (cookieConsent) {
            // Make sure cookie content is properly handled for Safari
            const cookieContents = cookieConsent.querySelectorAll('.it-content, .en-content');
            cookieContents.forEach(el => {
                if (isSafari) {
                    // Make all hidden first
                    el.style.position = 'absolute';
                    el.style.opacity = '0';
                    el.style.pointerEvents = 'none';
                    el.style.visibility = 'hidden';
                    
                    // Then show only the selected language
                    if (el.classList.contains(`${language}-content`)) {
                        el.style.position = 'static';
                        el.style.opacity = '1';
                        el.style.pointerEvents = 'auto';
                        el.style.visibility = 'visible';
                    }
                }
            });
            
            // Ensure cookie consent doesn't block language buttons in Safari
            if (isSafari) {
                cookieConsent.style.pointerEvents = 'none';
                
                // But make sure the cookie buttons themselves can still receive clicks
                const cookieButtons = cookieConsent.querySelectorAll('.cookie-buttons button');
                cookieButtons.forEach(btn => {
                    btn.style.pointerEvents = 'auto';
                });
                
                const cookieLinks = cookieConsent.querySelectorAll('a');
                cookieLinks.forEach(link => {
                    link.style.pointerEvents = 'auto';
                });
            }
        }
        
        // For Safari compatibility, use visibility approach rather than display property
        document.querySelectorAll('.it-content, .en-content').forEach(el => {
            el.classList.remove('lang-visible');
            el.classList.add('lang-hidden');
            
            // For Safari, we need a more direct approach
            if (isSafari) {
                el.style.position = 'absolute';
                el.style.opacity = '0';
                el.style.pointerEvents = 'none';
                el.style.visibility = 'hidden';
            }
        });
        
        // Show content for selected language
        document.querySelectorAll(`.${language}-content`).forEach(el => {
            el.classList.remove('lang-hidden');
            el.classList.add('lang-visible');
            
            // For Safari, restore normal display
            if (isSafari) {
                el.style.position = 'static';
                el.style.opacity = '1';
                el.style.pointerEvents = 'auto';
                el.style.visibility = 'visible';
            }
        });
        
        // Update active state on buttons
        languageButtons.forEach(button => {
            if (button.getAttribute('data-lang') === language) {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        });
        
        // Set the html lang attribute
        document.documentElement.lang = language;
        
        // Force Safari to repaint if necessary
        if (isSafari) {
            document.body.style.opacity = '0.999';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 10);
        }
    }
    
    // Add click event listeners to all language buttons
    languageButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event from bubbling up
            const language = this.getAttribute('data-lang');
            switchLanguage(language);
        });
    });
    
    // Initialize with saved language preference
    switchLanguage(savedLanguage);
});