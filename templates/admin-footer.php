    <!-- Footer -->
    <footer class="bg-black border-t border-gray-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="w-6 h-6 bg-gold rounded-md flex items-center justify-center mr-2">
                        <i class="fas fa-clock text-black text-sm"></i>
                    </div>
                    <span class="text-white font-medium">JackoTimespiece Admin</span>
                </div>
                <div class="text-gray-400 text-sm">
                    &copy; <?php echo date('Y'); ?> JackoTimespiece. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-auto-hide');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });

        // Confirm delete actions
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        }

        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('translate-x-0');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            
            if (sidebar && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
            }
        });

        // Auto-refresh notifications
        setInterval(function() {
            // You can add AJAX call here to refresh notifications
            // fetch('/api/admin/notifications')
            //     .then(response => response.json())
            //     .then(data => updateNotifications(data));
        }, 30000); // Refresh every 30 seconds

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Ctrl/Cmd + K to open search
            if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
                event.preventDefault();
                // Open search modal or focus search input
                const searchInput = document.getElementById('search-input');
                if (searchInput) {
                    searchInput.focus();
                }
            }
            
            // Escape to close modals
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(function(modal) {
                    if (modal.classList.contains('show')) {
                        modal.classList.remove('show');
                    }
                });
            }
        });

        // Form validation helpers
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return true;

            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(function(input) {
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            return isValid;
        }

        // AJAX form submission helper
        function submitFormAjax(formId, successCallback, errorCallback) {
            const form = document.getElementById(formId);
            if (!form) return;

            const formData = new FormData(form);
            
            fetch(form.action, {
                method: form.method,
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (successCallback) successCallback(data);
                } else {
                    if (errorCallback) errorCallback(data.error);
                }
            })
            .catch(error => {
                if (errorCallback) errorCallback('An error occurred. Please try again.');
            });
        }

        // Toast notification helper
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white transition-all duration-300 transform translate-x-full`;
            
            switch (type) {
                case 'success':
                    toast.classList.add('bg-green-600');
                    break;
                case 'error':
                    toast.classList.add('bg-red-600');
                    break;
                case 'warning':
                    toast.classList.add('bg-yellow-600');
                    break;
                default:
                    toast.classList.add('bg-blue-600');
            }
            
            toast.textContent = message;
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 5000);
        }

        // Table sorting helper
        function sortTable(tableId, columnIndex) {
            const table = document.getElementById(tableId);
            if (!table) return;

            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                const aValue = a.cells[columnIndex].textContent.trim();
                const bValue = b.cells[columnIndex].textContent.trim();
                
                // Try to parse as numbers first
                const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
                const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
                
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return aNum - bNum;
                }
                
                // Fall back to string comparison
                return aValue.localeCompare(bValue);
            });
            
            // Clear and re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }

        // Search table helper
        function searchTable(tableId, searchTerm) {
            const table = document.getElementById(tableId);
            if (!table) return;

            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const match = text.includes(searchTerm.toLowerCase());
                row.style.display = match ? '' : 'none';
            });
        }
    </script>

    <!-- Additional page-specific scripts -->
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html> 