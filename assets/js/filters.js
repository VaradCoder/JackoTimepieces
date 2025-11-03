/**
 * Product Filters and Search JavaScript for JackoTimespiece
 * Handles product filtering, search, and catalog interactions
 */

class ProductFilters {
    constructor() {
        this.currentFilters = {
            category: '',
            brand: '',
            price_min: '',
            price_max: '',
            sort: 'newest',
            search: '',
            gender: '',
            page: 1
        };
        this.products = [];
        this.totalProducts = 0;
        this.productsPerPage = 12;
        this.isLoading = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeFilters();
        this.loadProducts();
    }

    bindEvents() {
        // Filter form submission
        document.addEventListener('submit', (e) => {
            if (e.target.matches('.filter-form')) {
                e.preventDefault();
                this.applyFilters();
            }
        });

        // Filter changes
        document.addEventListener('change', (e) => {
            if (e.target.matches('.filter-select, .filter-checkbox, .filter-radio')) {
                this.handleFilterChange(e.target);
            }
        });

        // Search input
        document.addEventListener('input', (e) => {
            if (e.target.matches('.search-input')) {
                this.handleSearchInput(e.target);
            }
        });

        // Price range sliders
        document.addEventListener('input', (e) => {
            if (e.target.matches('.price-range')) {
                this.handlePriceRangeChange(e.target);
            }
        });

        // Sort dropdown
        document.addEventListener('change', (e) => {
            if (e.target.matches('.sort-select')) {
                this.currentFilters.sort = e.target.value;
                this.loadProducts();
            }
        });

        // Pagination
        document.addEventListener('click', (e) => {
            if (e.target.matches('.pagination-link')) {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                this.goToPage(page);
            }
        });

        // Quick filters
        document.addEventListener('click', (e) => {
            if (e.target.matches('.quick-filter')) {
                e.preventDefault();
                const filter = e.target.dataset.filter;
                const value = e.target.dataset.value;
                this.applyQuickFilter(filter, value);
            }
        });

        // Clear filters
        document.addEventListener('click', (e) => {
            if (e.target.matches('.clear-filters')) {
                e.preventDefault();
                this.clearFilters();
            }
        });

        // Toggle filter sidebar on mobile
        document.addEventListener('click', (e) => {
            if (e.target.matches('.filter-toggle')) {
                e.preventDefault();
                this.toggleFilterSidebar();
            }
        });

        // Close filter sidebar when clicking outside
        document.addEventListener('click', (e) => {
            const filterSidebar = document.querySelector('.filter-sidebar');
            const filterToggle = document.querySelector('.filter-toggle');
            
            if (filterSidebar && !filterSidebar.contains(e.target) && !filterToggle.contains(e.target)) {
                this.closeFilterSidebar();
            }
        });

        // Infinite scroll
        window.addEventListener('scroll', () => {
            if (this.shouldLoadMore()) {
                this.loadMoreProducts();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.querySelector('.search-input');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
    }

    initializeFilters() {
        // Set initial values from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        
        this.currentFilters = {
            category: urlParams.get('category') || '',
            brand: urlParams.get('brand') || '',
            price_min: urlParams.get('price_min') || '',
            price_max: urlParams.get('price_max') || '',
            sort: urlParams.get('sort') || 'newest',
            search: urlParams.get('search') || '',
            gender: urlParams.get('gender') || '',
            page: parseInt(urlParams.get('page')) || 1
        };

        // Update form elements
        this.updateFilterForm();
    }

    updateFilterForm() {
        // Update select elements
        Object.keys(this.currentFilters).forEach(key => {
            const element = document.querySelector(`[name="${key}"]`);
            if (element && this.currentFilters[key]) {
                element.value = this.currentFilters[key];
            }
        });

        // Update checkboxes
        const checkboxes = document.querySelectorAll('.filter-checkbox');
        checkboxes.forEach(checkbox => {
            const filterName = checkbox.name;
            const filterValue = checkbox.value;
            if (this.currentFilters[filterName] === filterValue) {
                checkbox.checked = true;
            }
        });

        // Update price range displays
        this.updatePriceRangeDisplay();
    }

    handleFilterChange(element) {
        const name = element.name;
        const value = element.type === 'checkbox' ? (element.checked ? element.value : '') : element.value;
        
        this.currentFilters[name] = value;
        
        // Reset to first page when filters change
        this.currentFilters.page = 1;
        
        // Debounce the filter application
        clearTimeout(this.filterTimeout);
        this.filterTimeout = setTimeout(() => {
            this.loadProducts();
        }, 300);
    }

    handleSearchInput(input) {
        this.currentFilters.search = input.value;
        this.currentFilters.page = 1;
        
        // Debounce search
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.loadProducts();
        }, 500);
    }

    handlePriceRangeChange(slider) {
        const name = slider.name;
        this.currentFilters[name] = slider.value;
        this.updatePriceRangeDisplay();
        
        // Debounce price filter
        clearTimeout(this.priceTimeout);
        this.priceTimeout = setTimeout(() => {
            this.loadProducts();
        }, 500);
    }

    updatePriceRangeDisplay() {
        const minDisplay = document.querySelector('.price-min-display');
        const maxDisplay = document.querySelector('.price-max-display');
        
        if (minDisplay && this.currentFilters.price_min) {
            minDisplay.textContent = this.formatCurrency(this.currentFilters.price_min);
        }
        
        if (maxDisplay && this.currentFilters.price_max) {
            maxDisplay.textContent = this.formatCurrency(this.currentFilters.price_max);
        }
    }

    async loadProducts() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoadingState();

        try {
            const queryString = this.buildQueryString();
            const response = await fetch(`../api/products/list.php?${queryString}`);
            const data = await response.json();

            if (data.success) {
                this.products = data.products;
                this.totalProducts = data.total;
                this.renderProducts();
                this.updatePagination();
                this.updateResultsCount();
                this.updateURL();
            } else {
                this.showError(data.error || 'Failed to load products');
            }
        } catch (error) {
            console.error('Error loading products:', error);
            this.showError('Failed to load products. Please try again.');
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
        }
    }

    async loadMoreProducts() {
        if (this.isLoading) return;
        
        this.currentFilters.page++;
        this.isLoading = true;
        this.showLoadMoreState();

        try {
            const queryString = this.buildQueryString();
            const response = await fetch(`../api/products/list.php?${queryString}`);
            const data = await response.json();

            if (data.success) {
                this.products = [...this.products, ...data.products];
                this.renderProducts(true); // Append mode
                this.updateURL();
            }
        } catch (error) {
            console.error('Error loading more products:', error);
            this.currentFilters.page--; // Revert page increment
        } finally {
            this.isLoading = false;
            this.hideLoadMoreState();
        }
    }

    renderProducts(append = false) {
        const container = document.querySelector('.products-grid');
        if (!container) return;

        if (!append) {
            container.innerHTML = '';
        }

        if (this.products.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-300 mb-2">No products found</h3>
                    <p class="text-gray-400 mb-4">Try adjusting your filters or search terms</p>
                    <button class="clear-filters bg-gold text-black px-4 py-2 rounded-lg hover:bg-white transition">
                        Clear Filters
                    </button>
                </div>
            `;
            return;
        }

        this.products.forEach(product => {
            const productCard = this.createProductCard(product);
            container.appendChild(productCard);
        });

        // Animate new products
        if (append) {
            const newProducts = container.querySelectorAll('.product-card:not(.animated)');
            newProducts.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, index * 100);
            });
        }
    }

    createProductCard(product) {
        const card = document.createElement('div');
        card.className = 'product-card bg-gray-800 rounded-lg border border-gray-700 overflow-hidden hover:border-gold transition-all duration-300 transform hover:scale-105';
        card.dataset.productId = product.id;

        const discountPercentage = product.original_price ? 
            Math.round((product.original_price - product.price) / product.original_price * 100) : 0;

        card.innerHTML = `
            <div class="relative">
                <img src="../assets/images/watches/${product.image}" 
                     alt="${product.name}" 
                     class="w-full h-64 object-cover">
                ${discountPercentage > 0 ? `
                    <div class="absolute top-2 left-2 bg-red-600 text-white px-2 py-1 rounded text-sm font-medium">
                        -${discountPercentage}%
                    </div>
                ` : ''}
                <button class="wishlist-btn absolute top-2 right-2 w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 transition"
                        data-watch-id="${product.id}">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <h3 class="text-white font-medium mb-2 line-clamp-2">${product.name}</h3>
                <p class="text-gray-400 text-sm mb-2">${product.brand}</p>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-gold font-bold">${this.formatCurrency(product.price)}</span>
                        ${product.original_price ? `
                            <span class="text-gray-500 line-through text-sm">${this.formatCurrency(product.original_price)}</span>
                        ` : ''}
                    </div>
                    <div class="flex items-center">
                        ${this.generateRatingStars(product.rating?.average || 0)}
                        <span class="text-gray-400 text-sm ml-1">(${product.rating?.total || 0})</span>
                    </div>
                </div>
                <button class="add-to-cart w-full bg-gold text-black py-2 rounded-lg hover:bg-white transition font-medium"
                        data-watch-id="${product.id}">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Add to Cart
                </button>
            </div>
        `;

        return card;
    }

    generateRatingStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

        let stars = '';
        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star text-gold text-sm"></i>';
        }
        if (hasHalfStar) {
            stars += '<i class="fas fa-star-half-alt text-gold text-sm"></i>';
        }
        for (let i = 0; i < emptyStars; i++) {
            stars += '<i class="far fa-star text-gray-400 text-sm"></i>';
        }

        return stars;
    }

    updatePagination() {
        const paginationContainer = document.querySelector('.pagination');
        if (!paginationContainer) return;

        const totalPages = Math.ceil(this.totalProducts / this.productsPerPage);
        const currentPage = this.currentFilters.page;

        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let paginationHTML = '<div class="flex justify-center space-x-2">';

        // Previous button
        if (currentPage > 1) {
            paginationHTML += `
                <a href="#" class="pagination-link px-3 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition"
                   data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            `;
        }

        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <a href="#" class="pagination-link px-3 py-2 rounded transition ${
                    i === currentPage 
                        ? 'bg-gold text-black' 
                        : 'bg-gray-800 text-white hover:bg-gray-700'
                }" data-page="${i}">
                    ${i}
                </a>
            `;
        }

        // Next button
        if (currentPage < totalPages) {
            paginationHTML += `
                <a href="#" class="pagination-link px-3 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition"
                   data-page="${currentPage + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            `;
        }

        paginationHTML += '</div>';
        paginationContainer.innerHTML = paginationHTML;
    }

    updateResultsCount() {
        const resultsCount = document.querySelector('.results-count');
        if (resultsCount) {
            const start = (this.currentFilters.page - 1) * this.productsPerPage + 1;
            const end = Math.min(start + this.productsPerPage - 1, this.totalProducts);
            resultsCount.textContent = `Showing ${start}-${end} of ${this.totalProducts} products`;
        }
    }

    updateURL() {
        const url = new URL(window.location);
        
        Object.keys(this.currentFilters).forEach(key => {
            if (this.currentFilters[key]) {
                url.searchParams.set(key, this.currentFilters[key]);
            } else {
                url.searchParams.delete(key);
            }
        });

        // Don't add page=1 to URL
        if (this.currentFilters.page === 1) {
            url.searchParams.delete('page');
        }

        window.history.replaceState({}, '', url);
    }

    buildQueryString() {
        const params = new URLSearchParams();
        
        Object.keys(this.currentFilters).forEach(key => {
            if (this.currentFilters[key]) {
                params.append(key, this.currentFilters[key]);
            }
        });

        return params.toString();
    }

    applyFilters() {
        const form = document.querySelector('.filter-form');
        if (!form) return;

        const formData = new FormData(form);
        
        Object.keys(this.currentFilters).forEach(key => {
            if (formData.has(key)) {
                this.currentFilters[key] = formData.get(key);
            }
        });

        this.currentFilters.page = 1;
        this.loadProducts();
    }

    applyQuickFilter(filter, value) {
        this.currentFilters[filter] = value;
        this.currentFilters.page = 1;
        this.loadProducts();
    }

    clearFilters() {
        this.currentFilters = {
            category: '',
            brand: '',
            price_min: '',
            price_max: '',
            sort: 'newest',
            search: '',
            gender: '',
            page: 1
        };

        // Reset form
        const form = document.querySelector('.filter-form');
        if (form) {
            form.reset();
        }

        // Clear search input
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.value = '';
        }

        this.loadProducts();
    }

    goToPage(page) {
        this.currentFilters.page = page;
        this.loadProducts();
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    toggleFilterSidebar() {
        const sidebar = document.querySelector('.filter-sidebar');
        if (sidebar) {
            sidebar.classList.toggle('translate-x-0');
            sidebar.classList.toggle('-translate-x-full');
        }
    }

    closeFilterSidebar() {
        const sidebar = document.querySelector('.filter-sidebar');
        if (sidebar) {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
        }
    }

    shouldLoadMore() {
        if (this.isLoading) return false;
        
        const scrollPosition = window.scrollY + window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        
        return scrollPosition >= documentHeight - 100;
    }

    showLoadingState() {
        const container = document.querySelector('.products-grid');
        if (container) {
            const loader = document.createElement('div');
            loader.className = 'col-span-full flex justify-center py-8';
            loader.innerHTML = '<i class="fas fa-spinner fa-spin text-gold text-2xl"></i>';
            container.appendChild(loader);
        }
    }

    hideLoadingState() {
        const loader = document.querySelector('.products-grid .fa-spinner');
        if (loader) {
            loader.closest('div').remove();
        }
    }

    showLoadMoreState() {
        const container = document.querySelector('.products-grid');
        if (container) {
            const loader = document.createElement('div');
            loader.className = 'col-span-full flex justify-center py-4';
            loader.innerHTML = '<i class="fas fa-spinner fa-spin text-gold"></i>';
            container.appendChild(loader);
        }
    }

    hideLoadMoreState() {
        const loader = document.querySelector('.products-grid .fa-spinner');
        if (loader) {
            loader.closest('div').remove();
        }
    }

    showError(message) {
        const container = document.querySelector('.products-grid');
        if (container) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-300 mb-2">Error Loading Products</h3>
                    <p class="text-gray-400 mb-4">${message}</p>
                    <button onclick="productFilters.loadProducts()" class="bg-gold text-black px-4 py-2 rounded-lg hover:bg-white transition">
                        Try Again
                    </button>
                </div>
            `;
        }
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            minimumFractionDigits: 0
        }).format(amount);
    }
}

// Initialize product filters when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.productFilters = new ProductFilters();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductFilters;
} 