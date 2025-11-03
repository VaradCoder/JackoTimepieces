<?php
/**
 * Admin Products List Page for JackoTimespiece
 * Handles product listing, search, filtering, and bulk operations
 */

require_once '../../core/config/app.php';
require_once '../../core/db/connection.php';
require_once '../../core/middleware/auth-admin.php';

// Require admin authentication
requireAdminAuth();

// Require product management permission
requireAdminPermission('manage_products');

$conn = getConnection();

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $selectedIds = $_POST['selected_products'] ?? [];
    
    if (!empty($selectedIds)) {
        switch ($action) {
            case 'delete':
                foreach ($selectedIds as $id) {
                    $stmt = $conn->prepare('DELETE FROM watches WHERE id = ?');
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                }
                $success = 'Selected products deleted successfully';
                break;
                
            case 'activate':
                $stmt = $conn->prepare('UPDATE watches SET status = "active" WHERE id IN (' . str_repeat('?,', count($selectedIds) - 1) . '?)');
                $stmt->bind_param(str_repeat('i', count($selectedIds)), ...$selectedIds);
                $stmt->execute();
                $success = 'Selected products activated successfully';
                break;
                
            case 'deactivate':
                $stmt = $conn->prepare('UPDATE watches SET status = "inactive" WHERE id IN (' . str_repeat('?,', count($selectedIds) - 1) . '?)');
                $stmt->bind_param(str_repeat('i', count($selectedIds)), ...$selectedIds);
                $stmt->execute();
                $success = 'Selected products deactivated successfully';
                break;
        }
    }
}

// Get filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$brand = $_GET['brand'] ?? '';
$status = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query
$whereConditions = ['1=1'];
$params = [];
$types = '';

if (!empty($search)) {
    $whereConditions[] = '(w.name LIKE ? OR w.brand LIKE ? OR w.model LIKE ?)';
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sss';
}

if (!empty($category)) {
    $whereConditions[] = 'w.category_id = ?';
    $params[] = $category;
    $types .= 'i';
}

if (!empty($brand)) {
    $whereConditions[] = 'w.brand = ?';
    $params[] = $brand;
    $types .= 's';
}

if (!empty($status)) {
    $whereConditions[] = 'w.status = ?';
    $params[] = $status;
    $types .= 's';
}

$whereClause = implode(' AND ', $whereConditions);

// Get total count
$countSql = "SELECT COUNT(*) as total FROM watches w WHERE $whereClause";
$stmt = $conn->prepare($countSql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$totalResult = $stmt->get_result()->fetch_assoc();
$total = $totalResult['total'];

// Get products
$orderBy = match($sort) {
    'name' => 'w.name ASC',
    'brand' => 'w.brand ASC',
    'price_low' => 'w.price ASC',
    'price_high' => 'w.price DESC',
    'oldest' => 'w.created_at ASC',
    default => 'w.created_at DESC'
};

$sql = "
    SELECT w.*, c.name as category_name,
           (SELECT COUNT(*) FROM reviews WHERE watch_id = w.id) as review_count,
           (SELECT AVG(rating) FROM reviews WHERE watch_id = w.id AND status = 'approved') as avg_rating
    FROM watches w
    LEFT JOIN categories c ON w.category_id = c.id
    WHERE $whereClause
    ORDER BY $orderBy
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get categories for filter
$stmt = $conn->prepare('SELECT id, name FROM categories ORDER BY name');
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get brands for filter
$stmt = $conn->prepare('SELECT DISTINCT brand FROM watches WHERE brand IS NOT NULL ORDER BY brand');
$stmt->execute();
$brands = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = "Products";
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../admin/index.php'],
    ['title' => 'Products']
];

include '../../templates/admin-header.php';
?>

<div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <div class="bg-black border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-white">Products</h1>
                    <p class="text-gray-400">Manage your product catalog</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="add.php" class="bg-gold text-black px-4 py-2 rounded-lg hover:bg-white transition">
                        <i class="fas fa-plus mr-2"></i>
                        Add Product
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                    <input type="text" 
                           name="search" 
                           value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Search products..."
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                    <select name="category" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Brand -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Brand</label>
                    <select name="brand" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $brandItem): ?>
                        <option value="<?php echo htmlspecialchars($brandItem['brand']); ?>" <?php echo $brand === $brandItem['brand'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($brandItem['brand']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="">All Status</option>
                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Sort</label>
                    <select name="sort" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest</option>
                        <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                        <option value="brand" <?php echo $sort === 'brand' ? 'selected' : ''; ?>>Brand A-Z</option>
                        <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price Low-High</option>
                        <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price High-Low</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="lg:col-span-6 flex justify-between items-end">
                    <button type="submit" class="bg-gold text-black px-6 py-2 rounded-lg hover:bg-white transition">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>
                    <a href="list.php" class="text-gray-400 hover:text-white transition">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Results Count -->
        <div class="flex justify-between items-center mb-4">
            <p class="text-gray-400">
                Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $limit, $total); ?> of <?php echo number_format($total); ?> products
            </p>
            <div class="flex items-center space-x-2">
                <span class="text-gray-400 text-sm">Bulk Actions:</span>
                <select id="bulk-action" class="px-3 py-1 bg-gray-700 border border-gray-600 rounded text-white text-sm">
                    <option value="">Select Action</option>
                    <option value="activate">Activate</option>
                    <option value="deactivate">Deactivate</option>
                    <option value="delete">Delete</option>
                </select>
                <button onclick="applyBulkAction()" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">
                    Apply
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <form id="bulk-form" method="POST">
                <input type="hidden" name="bulk_action" id="bulk-action-input">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="select-all" class="rounded border-gray-600 bg-gray-700 text-gold focus:ring-gold">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Product
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Brand
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Stock
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-box text-4xl mb-4"></i>
                                <p>No products found</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-gray-700 transition">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="selected_products[]" value="<?php echo $product['id']; ?>" class="product-checkbox rounded border-gray-600 bg-gray-700 text-gold focus:ring-gold">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="../../assets/images/watches/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         class="w-12 h-12 object-cover rounded-lg mr-4">
                                    <div>
                                        <div class="text-white font-medium"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <div class="text-gray-400 text-sm"><?php echo htmlspecialchars($product['model']); ?></div>
                                        <div class="text-gray-500 text-xs">
                                            <?php echo $product['review_count']; ?> reviews
                                            <?php if ($product['avg_rating']): ?>
                                            • <?php echo round($product['avg_rating'], 1); ?> ★
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                <?php echo htmlspecialchars($product['brand']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium"><?php echo formatCurrency($product['price']); ?></div>
                                <?php if ($product['original_price']): ?>
                                <div class="text-gray-400 text-sm line-through"><?php echo formatCurrency($product['original_price']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php echo $product['stock_quantity'] <= 5 ? 'bg-red-900 text-red-200' : 'bg-green-900 text-green-200'; ?>">
                                    <?php echo $product['stock_quantity']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php echo $product['status'] === 'active' ? 'bg-green-900 text-green-200' : 'bg-gray-900 text-gray-200'; ?>">
                                    <?php echo ucfirst($product['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="edit.php?id=<?php echo $product['id']; ?>" 
                                       class="text-blue-400 hover:text-blue-300 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../../public/watch.php?id=<?php echo $product['id']; ?>" 
                                       target="_blank"
                                       class="text-green-400 hover:text-green-300 transition">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="deleteProduct(<?php echo $product['id']; ?>)" 
                                            class="text-red-400 hover:text-red-300 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>

        <!-- Pagination -->
        <?php if ($total > $limit): ?>
        <div class="mt-6 flex justify-center">
            <nav class="flex items-center space-x-2">
                <?php
                $totalPages = ceil($total / $limit);
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                ?>
                
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>&brand=<?php echo urlencode($brand); ?>&status=<?php echo $status; ?>&sort=<?php echo $sort; ?>" 
                   class="px-3 py-2 bg-gray-700 text-white rounded hover:bg-gray-600 transition">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>&brand=<?php echo urlencode($brand); ?>&status=<?php echo $status; ?>&sort=<?php echo $sort; ?>" 
                   class="px-3 py-2 rounded transition <?php echo $i === $page ? 'bg-gold text-black' : 'bg-gray-700 text-white hover:bg-gray-600'; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>&brand=<?php echo urlencode($brand); ?>&status=<?php echo $status; ?>&sort=<?php echo $sort; ?>" 
                   class="px-3 py-2 bg-gray-700 text-white rounded hover:bg-gray-600 transition">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Bulk action functionality
function applyBulkAction() {
    const action = document.getElementById('bulk-action').value;
    const selectedProducts = document.querySelectorAll('.product-checkbox:checked');
    
    if (!action) {
        alert('Please select an action');
        return;
    }
    
    if (selectedProducts.length === 0) {
        alert('Please select at least one product');
        return;
    }
    
    if (action === 'delete' && !confirm('Are you sure you want to delete the selected products? This action cannot be undone.')) {
        return;
    }
    
    document.getElementById('bulk-action-input').value = action;
    document.getElementById('bulk-form').submit();
}

// Delete product functionality
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="bulk_action" value="delete">
            <input type="hidden" name="selected_products[]" value="${productId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include '../../templates/admin-footer.php'; ?> 