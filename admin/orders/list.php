<?php
session_start();
require_once '../../core/db/connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_user'])) {
    header('Location: ../login.php');
    exit;
}

$message = '';

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];
    $tracking_number = $_POST['tracking_number'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    $stmt = $conn->prepare("UPDATE orders SET status = ?, tracking_number = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("sssi", $new_status, $tracking_number, $notes, $order_id);
    
    if ($stmt->execute()) {
        $message = 'Order status updated successfully!';
    } else {
        $message = 'Failed to update order status.';
    }
}

// Handle order assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_order'])) {
    $order_id = intval($_POST['order_id']);
    $assigned_to = intval($_POST['assigned_to']);
    
    $stmt = $conn->prepare("UPDATE orders SET assigned_to = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("ii", $assigned_to, $order_id);
    
    if ($stmt->execute()) {
        $message = 'Order assigned successfully!';
    } else {
        $message = 'Failed to assign order.';
    }
}

// Get filters
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where_conditions = [];
$params = [];
$param_types = '';

if ($status_filter) {
    $where_conditions[] = "o.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if ($date_filter) {
    $where_conditions[] = "DATE(o.created_at) = ?";
    $params[] = $date_filter;
    $param_types .= 's';
}

if ($search) {
    $where_conditions[] = "(o.order_id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ssss';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get orders
$query = "
    SELECT o.*, u.first_name, u.last_name, u.email, u.phone,
           COUNT(oi.id) as item_count,
           GROUP_CONCAT(CONCAT(w.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items_summary
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN watches w ON oi.watch_id = w.id
    $where_clause
    GROUP BY o.id
    ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get admin users for assignment
$stmt = $conn->prepare("SELECT id, first_name, last_name FROM admin_users WHERE is_active = 1");
$stmt->execute();
$admin_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Function to get status color
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Admin | JackoTimespiece</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --gold: #c9b37e; }
        .text-gold { color: var(--gold) !important; }
        .bg-gold { background: var(--gold) !important; }
        .border-gold { border-color: var(--gold) !important; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="bg-black border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-white">Order Management</h1>
                    <p class="text-gray-400">Manage and track all customer orders</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gold hover:text-white transition">Dashboard</a>
                    <a href="../logout.php" class="text-gray-400 hover:text-white transition">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Message -->
        <?php if ($message): ?>
            <div class="bg-gold text-black px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-info-circle mr-2"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Date</label>
                    <input type="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>" 
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Order ID, Customer Name, Email"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gold text-black px-4 py-2 rounded-lg hover:bg-white hover:text-gold transition">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-gray-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">#<?php echo htmlspecialchars($order['order_id']); ?></div>
                                    <div class="text-sm text-gray-400"><?php echo $order['item_count']; ?> items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">
                                        <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-400"><?php echo htmlspecialchars($order['email']); ?></div>
                                    <div class="text-sm text-gray-400"><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-300 max-w-xs truncate">
                                        <?php echo htmlspecialchars($order['items_summary']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gold">₹<?php echo number_format($order['total']); ?></div>
                                    <div class="text-sm text-gray-400"><?php echo ucfirst($order['payment_status']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo getStatusColor($order['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewOrder(<?php echo $order['id']; ?>)" 
                                                class="text-blue-400 hover:text-blue-300">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editOrder(<?php echo $order['id']; ?>)" 
                                                class="text-gold hover:text-yellow-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="assignOrder(<?php echo $order['id']; ?>)" 
                                                class="text-green-400 hover:text-green-300">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-400">
                Showing <?php echo count($orders); ?> orders
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-white">Order Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="order-details-content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Edit Order</h3>
                    <form id="edit-form" method="POST">
                        <input type="hidden" name="order_id" id="edit-order-id">
                        <input type="hidden" name="update_status" value="1">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                            <select name="new_status" id="edit-status" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Tracking Number</label>
                            <input type="text" name="tracking_number" id="edit-tracking" 
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Notes</label>
                            <textarea name="notes" id="edit-notes" rows="3" 
                                      class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white"></textarea>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-gold text-black px-4 py-2 rounded-lg hover:bg-white hover:text-gold transition">
                                Update Order
                            </button>
                            <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Order Modal -->
    <div id="assign-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Assign Order</h3>
                    <form id="assign-form" method="POST">
                        <input type="hidden" name="order_id" id="assign-order-id">
                        <input type="hidden" name="assign_order" value="1">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Assign to Admin</label>
                            <select name="assigned_to" id="assign-admin" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white">
                                <option value="">Select Admin</option>
                                <?php foreach ($admin_users as $admin): ?>
                                    <option value="<?php echo $admin['id']; ?>">
                                        <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-gold text-black px-4 py-2 rounded-lg hover:bg-white hover:text-gold transition">
                                Assign Order
                            </button>
                            <button type="button" onclick="closeAssignModal()" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // View order details
        function viewOrder(orderId) {
            // This would typically load order details via AJAX
            document.getElementById('order-details-content').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-info-circle text-gold text-4xl mb-4"></i>
                    <p class="text-gray-400">Detailed order information would be loaded here.</p>
                    <p class="text-gray-500 text-sm mt-2">This feature will show complete order details, items, shipping info, etc.</p>
                </div>
            `;
            document.getElementById('order-modal').classList.remove('hidden');
        }

        // Edit order
        function editOrder(orderId) {
            document.getElementById('edit-order-id').value = orderId;
            document.getElementById('edit-modal').classList.remove('hidden');
        }

        // Assign order
        function assignOrder(orderId) {
            document.getElementById('assign-order-id').value = orderId;
            document.getElementById('assign-modal').classList.remove('hidden');
        }

        // Close modals
        function closeModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        function closeAssignModal() {
            document.getElementById('assign-modal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed')) {
                e.target.classList.add('hidden');
            }
        });
    </script>
</body>
</html> 