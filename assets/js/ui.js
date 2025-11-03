/**
 * UI Interactions and Animations for JackoTimespiece
 * Handles UI interactions, animations, and user experience enhancements
 */

class UI {
    constructor() {
        this.isScrolled = false;
        this.isMobileMenuOpen = false;
        this.currentTheme = 'dark';
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeAnimations();
        this.setupIntersectionObserver();
        this.initializeTooltips();
        this.initializeModals();
        this.initializeDropdowns();
        this.initializeSliders();
        this.initializeLazyLoading();
        this.initializeSmoothScrolling();
        this.initializeBackToTop();
        this.initializeLoadingStates();
        this.initializeFormValidation();
        this.initializeImageZoom();
        this.initializeStickyHeader();
    }

    bindEvents() {
        // Mobile menu toggle
        document.addEventListener('click', (e) => {
            if (e.target.matches('.mobile-menu-toggle') || e.target.closest('.mobile-menu-toggle')) {
                e.preventDefault();
                this.toggleMobileMenu();
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isMobileMenuOpen && !e.target.closest('.mobile-menu')) {
                this.closeMobileMenu();
            }
        });

        // Scroll events
        window.addEventListener('scroll', () => {
            this.handleScroll();
        });

        // Resize events
        window.addEventListener('resize', () => {
            this.handleResize();
        });

        // Keyboard events
        document.addEventListener('keydown', (e) => {
            this.handleKeyboard(e);
        });

        // Form submissions
        document.addEventListener('submit', (e) => {
            this.handleFormSubmit(e);
        });

        // Link clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href^="#"]')) {
                e.preventDefault();
                this.smoothScrollTo(e.target.getAttribute('href'));
            }
        });

        // Image lazy loading
        document.addEventListener('scroll', () => {
            this.loadLazyImages();
        });

        // Theme toggle
        document.addEventListener('click', (e) => {
            if (e.target.matches('.theme-toggle')) {
                e.preventDefault();
                this.toggleTheme();
            }
        });

        // Back to top
        document.addEventListener('click', (e) => {
            if (e.target.matches('.back-to-top')) {
                e.preventDefault();
                this.scrollToTop();
            }
        });

        // Modal triggers
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal]')) {
                e.preventDefault();
                this.openModal(e.target.dataset.modal);
            }
        });

        // Modal close
        document.addEventListener('click', (e) => {
            if (e.target.matches('.modal-close') || e.target.closest('.modal-close')) {
                e.preventDefault();
                this.closeModal();
            }
        });

        // Dropdown toggles
        document.addEventListener('click', (e) => {
            if (e.target.matches('.dropdown-toggle')) {
                e.preventDefault();
                this.toggleDropdown(e.target);
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown')) {
                this.closeAllDropdowns();
            }
        });

        // Tab navigation
        document.addEventListener('click', (e) => {
            if (e.target.matches('.tab-trigger')) {
                e.preventDefault();
                this.switchTab(e.target);
            }
        });

        // Accordion toggles
        document.addEventListener('click', (e) => {
            if (e.target.matches('.accordion-trigger')) {
                e.preventDefault();
                this.toggleAccordion(e.target);
            }
        });

        // Slider controls
        document.addEventListener('click', (e) => {
            if (e.target.matches('.slider-prev')) {
                e.preventDefault();
                this.sliderPrev(e.target.closest('.slider'));
            }
            if (e.target.matches('.slider-next')) {
                e.preventDefault();
                this.sliderNext(e.target.closest('.slider'));
            }
        });

        // Search functionality
        document.addEventListener('input', (e) => {
            if (e.target.matches('.search-input')) {
                this.handleSearchInput(e.target);
            }
        });

        // Loading states
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-loading')) {
                this.showButtonLoading(e.target);
            }
        });
    }

    initializeAnimations() {
        // Initialize Anime.js animations
        if (typeof anime !== 'undefined') {
            // Hero section animations
            const heroElements = document.querySelectorAll('.hero-animate');
            if (heroElements.length > 0) {
                anime.timeline()
                    .add({
                        targets: '.hero-title',
                        translateY: [50, 0],
                        opacity: [0, 1],
                        duration: 800,
                        easing: 'easeOutCubic'
                    })
                    .add({
                        targets: '.hero-subtitle',
                        translateY: [30, 0],
                        opacity: [0, 1],
                        duration: 600,
                        easing: 'easeOutCubic'
                    }, '-=400')
                    .add({
                        targets: '.hero-cta',
                        translateY: [20, 0],
                        opacity: [0, 1],
                        duration: 600,
                        easing: 'easeOutCubic'
                    }, '-=300');
            }

            // Product card animations
            const productCards = document.querySelectorAll('.product-card');
            if (productCards.length > 0) {
                anime({
                    targets: productCards,
                    translateY: [50, 0],
                    opacity: [0, 1],
                    duration: 800,
                    easing: 'easeOutCubic',
                    delay: anime.stagger(100)
                });
            }

            // Counter animations
            const counters = document.querySelectorAll('.counter');
            if (counters.length > 0) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const target = entry.target;
                            const finalValue = parseInt(target.dataset.value);
                            
                            anime({
                                targets: target,
                                innerHTML: [0, finalValue],
                                duration: 2000,
                                easing: 'easeOutCubic',
                                round: 1
                            });
                            
                            observer.unobserve(target);
                        }
                    });
                });
                
                counters.forEach(counter => observer.observe(counter));
            }
        }
    }

    setupIntersectionObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe elements with animation classes
        const animatedElements = document.querySelectorAll('.animate-on-scroll, .fade-in, .slide-up, .slide-left, .slide-right');
        animatedElements.forEach(el => observer.observe(el));
    }

    initializeTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target);
            });
            
            element.addEventListener('mouseleave', (e) => {
                this.hideTooltip(e.target);
            });
        });
    }

    showTooltip(element) {
        const tooltipText = element.dataset.tooltip;
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = tooltipText;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
        
        element.tooltip = tooltip;
    }

    hideTooltip(element) {
        if (element.tooltip) {
            element.tooltip.remove();
            element.tooltip = null;
        }
    }

    initializeModals() {
        // Create modal backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fixed inset-0 bg-black bg-opacity-50 z-50 hidden';
        backdrop.addEventListener('click', () => this.closeModal());
        document.body.appendChild(backdrop);
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.querySelector('.modal-backdrop');
        
        if (modal && backdrop) {
            backdrop.classList.remove('hidden');
            modal.classList.remove('hidden');
            
            // Animate in
            setTimeout(() => {
                backdrop.classList.add('opacity-100');
                modal.classList.add('scale-100', 'opacity-100');
            }, 10);
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal() {
        const modal = document.querySelector('.modal:not(.hidden)');
        const backdrop = document.querySelector('.modal-backdrop');
        
        if (modal && backdrop) {
            backdrop.classList.remove('opacity-100');
            modal.classList.remove('scale-100', 'opacity-100');
            
            setTimeout(() => {
                backdrop.classList.add('hidden');
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }
    }

    initializeDropdowns() {
        // Add dropdown functionality to elements with dropdown class
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            if (toggle && menu) {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleDropdown(toggle);
                });
            }
        });
    }

    toggleDropdown(toggle) {
        const dropdown = toggle.closest('.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        // Close other dropdowns
        this.closeAllDropdowns();
        
        // Toggle current dropdown
        menu.classList.toggle('hidden');
        toggle.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
    }

    closeAllDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown-menu:not(.hidden)');
        dropdowns.forEach(menu => {
            menu.classList.add('hidden');
            const toggle = menu.closest('.dropdown').querySelector('.dropdown-toggle');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    initializeSliders() {
        const sliders = document.querySelectorAll('.slider');
        sliders.forEach(slider => {
            const slides = slider.querySelectorAll('.slide');
            const prevBtn = slider.querySelector('.slider-prev');
            const nextBtn = slider.querySelector('.slider-next');
            const indicators = slider.querySelectorAll('.slider-indicator');
            
            let currentSlide = 0;
            
            const showSlide = (index) => {
                slides.forEach((slide, i) => {
                    slide.style.transform = `translateX(${(i - index) * 100}%)`;
                });
                
                indicators.forEach((indicator, i) => {
                    indicator.classList.toggle('active', i === index);
                });
            };
            
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                    showSlide(currentSlide);
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    currentSlide = (currentSlide + 1) % slides.length;
                    showSlide(currentSlide);
                });
            }
            
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    currentSlide = index;
                    showSlide(currentSlide);
                });
            });
            
            // Auto-play
            setInterval(() => {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }, 5000);
        });
    }

    initializeLazyLoading() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }

    initializeSmoothScrolling() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    initializeBackToTop() {
        const backToTop = document.querySelector('.back-to-top');
        if (backToTop) {
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    backToTop.classList.remove('hidden');
                } else {
                    backToTop.classList.add('hidden');
                }
            });
        }
    }

    initializeLoadingStates() {
        // Add loading states to buttons
        document.querySelectorAll('.btn-loading').forEach(button => {
            button.addEventListener('click', () => {
                this.showButtonLoading(button);
            });
        });
    }

    showButtonLoading(button) {
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        
        // Reset after form submission or timeout
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 5000);
    }

    initializeFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showFieldError(input, 'This field is required');
                isValid = false;
            } else {
                this.clearFieldError(input);
            }
        });
        
        return isValid;
    }

    showFieldError(input, message) {
        input.classList.add('border-red-500');
        let errorElement = input.parentNode.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'field-error text-red-500 text-sm mt-1';
            input.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    clearFieldError(input) {
        input.classList.remove('border-red-500');
        const errorElement = input.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    initializeImageZoom() {
        const zoomImages = document.querySelectorAll('.image-zoom');
        zoomImages.forEach(img => {
            img.addEventListener('mousemove', (e) => {
                const rect = img.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width * 100;
                const y = (e.clientY - rect.top) / rect.height * 100;
                
                img.style.transformOrigin = `${x}% ${y}%`;
            });
            
            img.addEventListener('mouseenter', () => {
                img.style.transform = 'scale(1.5)';
            });
            
            img.addEventListener('mouseleave', () => {
                img.style.transform = 'scale(1)';
            });
        });
    }

    initializeStickyHeader() {
        const header = document.querySelector('.sticky-header');
        if (header) {
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 100) {
                    header.classList.add('sticky', 'top-0', 'shadow-lg');
                } else {
                    header.classList.remove('sticky', 'top-0', 'shadow-lg');
                }
            });
        }
    }

    toggleMobileMenu() {
        const mobileMenu = document.querySelector('.mobile-menu');
        if (mobileMenu) {
            this.isMobileMenuOpen = !this.isMobileMenuOpen;
            mobileMenu.classList.toggle('hidden');
            
            // Animate menu items
            const menuItems = mobileMenu.querySelectorAll('.mobile-menu-item');
            menuItems.forEach((item, index) => {
                if (this.isMobileMenuOpen) {
                    item.style.animationDelay = `${index * 0.1}s`;
                    item.classList.add('animate-slide-in');
                } else {
                    item.classList.remove('animate-slide-in');
                }
            });
        }
    }

    closeMobileMenu() {
        const mobileMenu = document.querySelector('.mobile-menu');
        if (mobileMenu) {
            this.isMobileMenuOpen = false;
            mobileMenu.classList.add('hidden');
        }
    }

    handleScroll() {
        const scrollTop = window.pageYOffset;
        this.isScrolled = scrollTop > 50;
        
        // Update header appearance
        const header = document.querySelector('.header');
        if (header) {
            header.classList.toggle('scrolled', this.isScrolled);
        }
        
        // Parallax effects
        const parallaxElements = document.querySelectorAll('.parallax');
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrollTop * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    }

    handleResize() {
        // Close mobile menu on desktop
        if (window.innerWidth > 768 && this.isMobileMenuOpen) {
            this.closeMobileMenu();
        }
        
        // Recalculate slider positions
        this.updateSliderPositions();
    }

    handleKeyboard(e) {
        // Escape key closes modals and dropdowns
        if (e.key === 'Escape') {
            this.closeModal();
            this.closeAllDropdowns();
            this.closeMobileMenu();
        }
        
        // Ctrl/Cmd + K opens search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.focus();
            }
        }
    }

    handleFormSubmit(e) {
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn && !submitBtn.classList.contains('btn-loading')) {
            this.showButtonLoading(submitBtn);
        }
    }

    handleSearchInput(input) {
        const searchTerm = input.value.toLowerCase();
        const searchableElements = document.querySelectorAll('[data-search]');
        
        searchableElements.forEach(element => {
            const searchText = element.dataset.search.toLowerCase();
            const isVisible = searchText.includes(searchTerm);
            element.style.display = isVisible ? '' : 'none';
        });
    }

    smoothScrollTo(target) {
        const element = document.querySelector(target);
        if (element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    toggleTheme() {
        this.currentTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', this.currentTheme);
        localStorage.setItem('theme', this.currentTheme);
        
        // Update theme toggle button
        const themeToggle = document.querySelector('.theme-toggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = this.currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
    }

    switchTab(tabTrigger) {
        const tabGroup = tabTrigger.closest('.tab-group');
        const tabContent = tabGroup.querySelector('.tab-content');
        const targetTab = tabTrigger.dataset.tab;
        
        // Update active tab
        tabGroup.querySelectorAll('.tab-trigger').forEach(trigger => {
            trigger.classList.remove('active');
        });
        tabTrigger.classList.add('active');
        
        // Show target content
        tabContent.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.remove('active');
        });
        tabContent.querySelector(`[data-tab="${targetTab}"]`).classList.add('active');
    }

    toggleAccordion(trigger) {
        const accordion = trigger.closest('.accordion');
        const content = accordion.querySelector('.accordion-content');
        const isOpen = content.classList.contains('open');
        
        // Close other accordions
        accordion.parentNode.querySelectorAll('.accordion-content').forEach(item => {
            item.classList.remove('open');
        });
        
        // Toggle current accordion
        if (!isOpen) {
            content.classList.add('open');
        }
    }

    sliderPrev(slider) {
        const slides = slider.querySelectorAll('.slide');
        const currentSlide = slider.querySelector('.slide.active');
        const currentIndex = Array.from(slides).indexOf(currentSlide);
        const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
        
        this.showSlide(slider, prevIndex);
    }

    sliderNext(slider) {
        const slides = slider.querySelectorAll('.slide');
        const currentSlide = slider.querySelector('.slide.active');
        const currentIndex = Array.from(slides).indexOf(currentSlide);
        const nextIndex = (currentIndex + 1) % slides.length;
        
        this.showSlide(slider, nextIndex);
    }

    showSlide(slider, index) {
        const slides = slider.querySelectorAll('.slide');
        const indicators = slider.querySelectorAll('.slider-indicator');
        
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === index);
        });
    }

    updateSliderPositions() {
        const sliders = document.querySelectorAll('.slider');
        sliders.forEach(slider => {
            const activeSlide = slider.querySelector('.slide.active');
            if (activeSlide) {
                const index = Array.from(slider.querySelectorAll('.slide')).indexOf(activeSlide);
                this.showSlide(slider, index);
            }
        });
    }

    loadLazyImages() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }
}

// Initialize UI when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.ui = new UI();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UI;
} 