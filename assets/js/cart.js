/**
 * Cart Management JavaScript for JackoTimespiece
 * Handles cart operations, AJAX requests, and UI interactions
 */

class CartManager {
    constructor() {
        this.cartItems = [];
        this.cartTotal = 0;
        this.cartCount = 0;
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateCartDisplay();
        this.loadCartFromStorage();
    }

    bindEvents() {
        // Add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.add-to-cart') || e.target.closest('.add-to-cart')) {
                e.preventDefault();
                const button = e.target.matches('.add-to-cart') ? e.target : e.target.closest('.add-to-cart');
                const watchId = button.dataset.watchId;
                const quantity = parseInt(button.dataset.quantity) || 1;
                this.addToCart(watchId, quantity);
            }
        });

        // Remove from cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.remove-from-cart') || e.target.closest('.remove-from-cart')) {
                e.preventDefault();
                const button = e.target.matches('.remove-from-cart') ? e.target : e.target.closest('.remove-from-cart');
                const watchId = button.dataset.watchId;
                this.removeFromCart(watchId);
            }
        });

        // Update quantity buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.update-quantity') || e.target.closest('.update-quantity')) {
                e.preventDefault();
                const button = e.target.matches('.update-quantity') ? e.target : e.target.closest('.update-quantity');
                const watchId = button.dataset.watchId;
                const action = button.dataset.action;
                this.updateQuantity(watchId, action);
            }
        });

        // Quantity input changes
        document.addEventListener('change', (e) => {
            if (e.target.matches('.cart-quantity-input')) {
                const input = e.target;
                const watchId = input.dataset.watchId;
                const quantity = parseInt(input.value);
                this.updateCartQuantity(watchId, quantity);
            }
        });

        // Apply coupon
        document.addEventListener('submit', (e) => {
            if (e.target.matches('.apply-coupon-form')) {
                e.preventDefault();
                const form = e.target;
                const couponCode = form.querySelector('input[name="coupon_code"]').value;
                this.applyCoupon(couponCode);
            }
        });

        // Remove coupon
        document.addEventListener('click', (e) => {
            if (e.target.matches('.remove-coupon')) {
                e.preventDefault();
                this.removeCoupon();
            }
        });

        // Cart toggle
        document.addEventListener('click', (e) => {
            if (e.target.matches('.cart-toggle')) {
                e.preventDefault();
                this.toggleCart();
            }
        });

        // Close cart when clicking outside
        document.addEventListener('click', (e) => {
            const cart = document.querySelector('.cart-sidebar');
            const cartToggle = document.querySelector('.cart-toggle');
            
            if (cart && !cart.contains(e.target) && !cartToggle.contains(e.target)) {
                this.closeCart();
            }
        });
    }

    async addToCart(watchId, quantity = 1) {
        try {
            const button = document.querySelector(`[data-watch-id="${watchId}"].add-to-cart`);
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            }

            const response = await fetch('../api/cart/add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    watch_id: watchId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Item added to cart successfully!', 'success');
                this.updateCartData(data.cart_summary);
                this.updateCartDisplay();
                this.animateAddToCart(button);
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showNotification('Failed to add item to cart. Please try again.', 'error');
        } finally {
            const button = document.querySelector(`[data-watch-id="${watchId}"].add-to-cart`);
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
            }
        }
    }

    async removeFromCart(watchId) {
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }

        try {
            const response = await fetch('../api/cart/remove.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    watch_id: watchId
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Item removed from cart', 'success');
                this.updateCartData(data.cart_summary);
                this.updateCartDisplay();
                this.removeCartItem(watchId);
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
            this.showNotification('Failed to remove item from cart. Please try again.', 'error');
        }
    }

    async updateQuantity(watchId, action) {
        const currentQuantity = this.getCurrentQuantity(watchId);
        let newQuantity = currentQuantity;

        if (action === 'increase') {
            newQuantity = currentQuantity + 1;
        } else if (action === 'decrease') {
            newQuantity = Math.max(0, currentQuantity - 1);
        }

        if (newQuantity === 0) {
            this.removeFromCart(watchId);
            return;
        }

        await this.updateCartQuantity(watchId, newQuantity);
    }

    async updateCartQuantity(watchId, quantity) {
        try {
            const response = await fetch('../api/cart/update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    watch_id: watchId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartData(data.cart_summary);
                this.updateCartDisplay();
                this.updateQuantityDisplay(watchId, quantity);
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error updating cart:', error);
            this.showNotification('Failed to update cart. Please try again.', 'error');
        }
    }

    async applyCoupon(couponCode) {
        try {
            const response = await fetch('../api/coupons/apply.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    coupon_code: couponCode
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Coupon applied successfully!', 'success');
                this.updateCartDisplay();
                this.updateCouponDisplay(data.coupon);
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error applying coupon:', error);
            this.showNotification('Failed to apply coupon. Please try again.', 'error');
        }
    }

    async removeCoupon() {
        try {
            const response = await fetch('../api/coupons/remove.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Coupon removed', 'success');
                this.updateCartDisplay();
                this.removeCouponDisplay();
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error removing coupon:', error);
            this.showNotification('Failed to remove coupon. Please try again.', 'error');
        }
    }

    updateCartData(cartSummary) {
        this.cartCount = cartSummary.item_count || 0;
        this.cartTotal = cartSummary.total || 0;
        this.saveCartToStorage();
    }

    updateCartDisplay() {
        // Update cart count badge
        const cartBadge = document.querySelector('.cart-count-badge');
        if (cartBadge) {
            cartBadge.textContent = this.cartCount;
            cartBadge.style.display = this.cartCount > 0 ? 'block' : 'none';
        }

        // Update cart total
        const cartTotal = document.querySelector('.cart-total');
        if (cartTotal) {
            cartTotal.textContent = this.formatCurrency(this.cartTotal);
        }

        // Update cart items count
        const cartItemsCount = document.querySelector('.cart-items-count');
        if (cartItemsCount) {
            cartItemsCount.textContent = this.cartCount + ' item' + (this.cartCount !== 1 ? 's' : '');
        }
    }

    updateQuantityDisplay(watchId, quantity) {
        const input = document.querySelector(`[data-watch-id="${watchId}"].cart-quantity-input`);
        if (input) {
            input.value = quantity;
        }

        const display = document.querySelector(`[data-watch-id="${watchId}"].cart-quantity-display`);
        if (display) {
            display.textContent = quantity;
        }
    }

    updateCouponDisplay(coupon) {
        const couponContainer = document.querySelector('.applied-coupon');
        if (couponContainer) {
            couponContainer.innerHTML = `
                <div class="bg-green-900 border border-green-700 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-200 font-medium">Coupon Applied</p>
                            <p class="text-green-300 text-sm">${coupon.code} - ${this.formatCurrency(coupon.discount)} off</p>
                        </div>
                        <button class="remove-coupon text-green-400 hover:text-green-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        }
    }

    removeCouponDisplay() {
        const couponContainer = document.querySelector('.applied-coupon');
        if (couponContainer) {
            couponContainer.innerHTML = '';
        }
    }

    removeCartItem(watchId) {
        const cartItem = document.querySelector(`[data-watch-id="${watchId}"].cart-item`);
        if (cartItem) {
            cartItem.style.transition = 'all 0.3s ease';
            cartItem.style.transform = 'translateX(100%)';
            cartItem.style.opacity = '0';
            setTimeout(() => {
                cartItem.remove();
            }, 300);
        }
    }

    animateAddToCart(button) {
        if (!button) return;

        // Create flying cart animation
        const rect = button.getBoundingClientRect();
        const cartIcon = document.createElement('div');
        cartIcon.innerHTML = '<i class="fas fa-shopping-cart text-gold"></i>';
        cartIcon.style.cssText = `
            position: fixed;
            top: ${rect.top + rect.height / 2}px;
            left: ${rect.left + rect.width / 2}px;
            z-index: 9999;
            font-size: 20px;
            pointer-events: none;
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        `;
        document.body.appendChild(cartIcon);

        // Animate to cart
        const cartBadge = document.querySelector('.cart-count-badge');
        if (cartBadge) {
            const cartRect = cartBadge.getBoundingClientRect();
            setTimeout(() => {
                cartIcon.style.top = cartRect.top + cartRect.height / 2 + 'px';
                cartIcon.style.left = cartRect.left + cartRect.width / 2 + 'px';
                cartIcon.style.transform = 'scale(0.5)';
                cartIcon.style.opacity = '0';
            }, 100);

            setTimeout(() => {
                document.body.removeChild(cartIcon);
            }, 900);
        }
    }

    toggleCart() {
        const cart = document.querySelector('.cart-sidebar');
        if (cart) {
            cart.classList.toggle('translate-x-0');
            cart.classList.toggle('-translate-x-full');
        }
    }

    closeCart() {
        const cart = document.querySelector('.cart-sidebar');
        if (cart) {
            cart.classList.add('-translate-x-full');
            cart.classList.remove('translate-x-0');
        }
    }

    getCurrentQuantity(watchId) {
        const input = document.querySelector(`[data-watch-id="${watchId}"].cart-quantity-input`);
        return input ? parseInt(input.value) : 1;
    }

    loadCartFromStorage() {
        const stored = localStorage.getItem('jacko_cart');
        if (stored) {
            try {
                const cartData = JSON.parse(stored);
                this.cartCount = cartData.count || 0;
                this.cartTotal = cartData.total || 0;
                this.updateCartDisplay();
            } catch (error) {
                console.error('Error loading cart from storage:', error);
            }
        }
    }

    saveCartToStorage() {
        const cartData = {
            count: this.cartCount,
            total: this.cartTotal,
            timestamp: Date.now()
        };
        localStorage.setItem('jacko_cart', JSON.stringify(cartData));
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white transition-all duration-300 transform translate-x-full`;
        
        switch (type) {
            case 'success':
                notification.classList.add('bg-green-600');
                break;
            case 'error':
                notification.classList.add('bg-red-600');
                break;
            case 'warning':
                notification.classList.add('bg-yellow-600');
                break;
            default:
                notification.classList.add('bg-blue-600');
        }
        
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation' : 'info'}-circle mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    clearCart() {
        this.cartItems = [];
        this.cartTotal = 0;
        this.cartCount = 0;
        this.saveCartToStorage();
        this.updateCartDisplay();
        
        const cartContainer = document.querySelector('.cart-items');
        if (cartContainer) {
            cartContainer.innerHTML = '<p class="text-gray-400 text-center py-8">Your cart is empty</p>';
        }
    }
}

// Initialize cart manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.cartManager = new CartManager();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CartManager;
} 