<?php
/**
 * Admin Dashboard for JackoTimespiece
 * Main admin panel with statistics and overview
 */

require_once '../core/config/app.php';
require_once '../core/db/connection.php';
require_once '../core/middleware/auth-admin.php';

// Require admin authentication
requireAdminAuth();

// Get admin statistics
$conn = getConnection();
$stats = getAdminStats($conn);

// Get recent orders
$stmt = $conn->prepare('
    SELECT o.*, u.first_name, u.last_name, u.email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
');
$stmt->execute();
$recentOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get low stock products
$stmt = $conn->prepare('
    SELECT * FROM watches 
    WHERE stock_quantity <= 5 AND status = "active"
    ORDER BY stock_quantity ASC
    LIMIT 10
');
$stmt->execute();
$lowStockProducts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get recent activities
$stmt = $conn->prepare('
    SELECT al.*, u.first_name, u.last_name
    FROM activity_logs al
    LEFT JOIN users u ON al.user_id = u.id
    ORDER BY al.created_at DESC
    LIMIT 20
');
$stmt->execute();
$recentActivities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = "Admin Dashboard";
include '../templates/admin-header.php';
?>

<div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <div class="bg-black border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-white">Dashboard</h1>
                    <p class="text-gray-400">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['first_name']); ?>!</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-400"><?php echo date('l, F j, Y'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Products -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-box text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400">Total Products</p>
                        <p class="text-2xl font-bold text-white"><?php echo number_format($stats['total_products']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400">Total Orders</p>
                        <p class="text-2xl font-bold text-white"><?php echo number_format($stats['total_orders']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400">Total Customers</p>
                        <p class="text-2xl font-bold text-white"><?php echo number_format($stats['total_customers']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gold rounded-md flex items-center justify-center">
                            <i class="fas fa-rupee-sign text-black"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-bold text-white">₹<?php echo number_format($stats['total_revenue']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($stats['pending_orders'] > 0 || $stats['low_stock_products'] > 0): ?>
        <div class="mb-8">
            <div class="bg-yellow-900 border border-yellow-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-200">Attention Required</h3>
                        <div class="mt-2 text-sm text-yellow-100">
                            <ul class="list-disc list-inside">
                                <?php if ($stats['pending_orders'] > 0): ?>
                                <li><?php echo $stats['pending_orders']; ?> pending orders need attention</li>
                                <?php endif; ?>
                                <?php if ($stats['low_stock_products'] > 0): ?>
                                <li><?php echo $stats['low_stock_products']; ?> products are running low on stock</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Orders -->
            <div class="bg-gray-800 rounded-lg border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-medium text-white">Recent Orders</h3>
                </div>
                <div class="p-6">
                    <?php if (empty($recentOrders)): ?>
                        <p class="text-gray-400 text-center py-4">No recent orders</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentOrders as $order): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
                                <div>
                                    <p class="text-white font-medium">Order #<?php echo htmlspecialchars($order['order_id']); ?></p>
                                    <p class="text-gray-400 text-sm">
                                        <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                                    </p>
                                    <p class="text-gray-400 text-sm"><?php echo formatCurrency($order['total']); ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo getStatusColor($order['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                    </span>
                                    <p class="text-gray-400 text-sm mt-1">
                                        <?php echo formatDateTime($order['created_at']); ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="orders/list.php" class="text-gold hover:text-white transition">View all orders</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="bg-gray-800 rounded-lg border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-medium text-white">Low Stock Products</h3>
                </div>
                <div class="p-6">
                    <?php if (empty($lowStockProducts)): ?>
                        <p class="text-gray-400 text-center py-4">All products have sufficient stock</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($lowStockProducts as $product): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <img src="../assets/images/watches/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         class="w-12 h-12 object-cover rounded-lg">
                                    <div class="ml-4">
                                        <p class="text-white font-medium"><?php echo htmlspecialchars($product['name']); ?></p>
                                        <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($product['brand']); ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo $product['stock_quantity'] == 0 ? 'bg-red-900 text-red-200' : 'bg-yellow-900 text-yellow-200'; ?>">
                                        <?php echo $product['stock_quantity']; ?> left
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="products/list.php" class="text-gold hover:text-white transition">Manage products</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="mt-8 bg-gray-800 rounded-lg border border-gray-700">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-medium text-white">Recent Activities</h3>
            </div>
            <div class="p-6">
                <?php if (empty($recentActivities)): ?>
                    <p class="text-gray-400 text-center py-4">No recent activities</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex items-start space-x-4 p-4 bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white">
                                    <span class="font-medium">
                                        <?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?>
                                    </span>
                                    <span class="text-gray-400">
                                        <?php echo htmlspecialchars($activity['action']); ?>
                                    </span>
                                </p>
                                <?php if (!empty($activity['details'])): ?>
                                <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($activity['details']); ?></p>
                                <?php endif; ?>
                                <p class="text-gray-500 text-xs mt-1">
                                    <?php echo formatDateTime($activity['created_at']); ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'pending':
            return 'bg-yellow-900 text-yellow-200';
        case 'processing':
            return 'bg-blue-900 text-blue-200';
        case 'shipped':
            return 'bg-purple-900 text-purple-200';
        case 'delivered':
            return 'bg-green-900 text-green-200';
        case 'cancelled':
            return 'bg-red-900 text-red-200';
        default:
            return 'bg-gray-900 text-gray-200';
    }
}

include '../templates/admin-footer.php';
?> 