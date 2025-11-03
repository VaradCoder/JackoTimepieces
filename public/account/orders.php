<?php
session_start();
require_once '../../core/config/constants.php';
if (!isset($_SESSION['user'])) header('Location: ' . LOGIN_PAGE);
require_once '../../core/db/connection.php';

$user_id = $_SESSION['user']['id'];
$message = '';

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);
    
    // Check if order belongs to user and can be cancelled
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? AND status IN ('pending', 'processing')");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if ($order) {
        $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            $message = 'Order cancelled successfully!';
        } else {
            $message = 'Failed to cancel order. Please try again.';
        }
    } else {
        $message = 'Order cannot be cancelled or does not exist.';
    }
}

// Handle order status update (for admin-like functionality)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];
    
    // Validate status
    $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_status, $order_id, $user_id);
        if ($stmt->execute()) {
            $message = 'Order status updated successfully!';
        } else {
            $message = 'Failed to update order status.';
        }
    }
}

// Get user orders with items
$stmt = $conn->prepare("
    SELECT o.*, 
           COUNT(oi.id) as item_count,
           GROUP_CONCAT(CONCAT(w.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items_summary
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN watches w ON oi.watch_id = w.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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

// Function to format date
function formatDate($date) {
    return date('M j, Y', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History | JackoTimespiece</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
</head>
<body class="bg-black text-white font-sans min-h-screen">
    <?php require_once '../../templates/header.php'; ?>
    
    <section class="container mx-auto px-6 py-16">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-serif text-gold mb-2">Order History</h1>
                <p class="text-gray-400">Track your orders and manage their status</p>
            </div>

            <!-- Message -->
            <?php if ($message): ?>
                <div class="bg-gold text-black px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-info-circle mr-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Orders List -->
            <?php if (empty($orders)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-shopping-bag text-gray-600 text-6xl mb-4"></i>
                    <h3 class="text-xl text-gray-400 mb-4">No orders found</h3>
                    <p class="text-gray-500 mb-6">You haven't placed any orders yet.</p>
                    <a href="../../public/store.php" class="bg-gold text-black px-6 py-3 rounded-lg hover:bg-white hover:text-gold transition-colors duration-300">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($orders as $order): ?>
                        <div class="bg-gray-900 rounded-lg border border-gray-800 p-6 hover:border-gold transition-colors duration-300 order-card">
                            <!-- Order Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                                    <p class="text-gray-400 text-sm">Placed on <?php echo formatDate($order['created_at']); ?></p>
                                    <p class="text-gray-400 text-sm"><?php echo $order['item_count']; ?> item(s)</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?php echo getStatusColor($order['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                    </span>
                                    <p class="text-gold font-bold text-lg mt-1">₹<?php echo number_format($order['total']); ?></p>
                                </div>
                            </div>

                            <!-- Order Items Summary -->
                            <div class="mb-4">
                                <p class="text-gray-300 text-sm"><?php echo htmlspecialchars($order['items_summary']); ?></p>
                            </div>

                            <!-- Order Details -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 text-sm">
                                <div>
                                    <span class="text-gray-400">Payment Status:</span>
                                    <span class="text-white ml-2"><?php echo ucfirst($order['payment_status']); ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-400">Payment Method:</span>
                                    <span class="text-white ml-2"><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-400">Tracking:</span>
                                    <span class="text-white ml-2"><?php echo htmlspecialchars($order['tracking_number'] ?? 'Not available'); ?></span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-3">
                                <!-- View Details -->
                                <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)" 
                                        class="bg-transparent border border-gold text-gold px-4 py-2 rounded-lg hover:bg-gold hover:text-black transition-colors duration-300">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Details
                                </button>

                                <!-- Cancel Order (only for pending/processing) -->
                                <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
                                    <button onclick="cancelOrder(<?php echo $order['id']; ?>)" 
                                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-300">
                                        <i class="fas fa-times mr-2"></i>
                                        Cancel Order
                                    </button>
                                <?php endif; ?>

                                <!-- Edit Order Status (for admin-like functionality) -->
                                <?php if (in_array($order['status'], ['pending', 'processing', 'shipped'])): ?>
                                    <button onclick="editOrderStatus(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')" 
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-300">
                                        <i class="fas fa-edit mr-2"></i>
                                        Edit Status
                                    </button>
                                <?php endif; ?>

                                <!-- Track Order -->
                                <?php if ($order['tracking_number']): ?>
                                    <a href="#" onclick="trackOrder('<?php echo htmlspecialchars($order['tracking_number']); ?>')" 
                                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-300">
                                        <i class="fas fa-truck mr-2"></i>
                                        Track Order
                                    </a>
                                <?php endif; ?>

                                <!-- Reorder -->
                                <button onclick="reorder(<?php echo $order['id']; ?>)" 
                                        class="bg-gold text-black px-4 py-2 rounded-lg hover:bg-white hover:text-gold transition-colors duration-300">
                                    <i class="fas fa-redo mr-2"></i>
                                    Reorder
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Order Details Modal -->
    <div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
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

    <!-- Edit Status Modal -->
    <div id="status-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Edit Order Status</h3>
                    <form id="status-form" method="POST">
                        <input type="hidden" name="order_id" id="status-order-id">
                        <input type="hidden" name="update_order" value="1">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">New Status</label>
                            <select name="new_status" id="new-status" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:border-gold">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-gold text-black px-4 py-2 rounded-lg hover:bg-white hover:text-gold transition-colors duration-300">
                                Update Status
                            </button>
                            <button type="button" onclick="closeStatusModal()" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-300">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="cancel-form" method="POST" style="display: none;">
        <input type="hidden" name="order_id" id="cancel-order-id">
        <input type="hidden" name="cancel_order" value="1">
    </form>

    <script>
        // View order details
        function viewOrderDetails(orderId) {
            // This would typically load order details via AJAX
            // For now, we'll show a simple message
            document.getElementById('order-details-content').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-info-circle text-gold text-4xl mb-4"></i>
                    <p class="text-gray-400">Detailed order information would be loaded here.</p>
                    <p class="text-gray-500 text-sm mt-2">This feature will show complete order details, items, shipping info, etc.</p>
                </div>
            `;
            document.getElementById('order-modal').classList.remove('hidden');
        }

        // Close modal
        function closeModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }

        // Close status modal
        function closeStatusModal() {
            document.getElementById('status-modal').classList.add('hidden');
        }

        // Cancel order
        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                document.getElementById('cancel-order-id').value = orderId;
                document.getElementById('cancel-form').submit();
            }
        }

        // Edit order status
        function editOrderStatus(orderId, currentStatus) {
            document.getElementById('status-order-id').value = orderId;
            document.getElementById('new-status').value = currentStatus;
            document.getElementById('status-modal').classList.remove('hidden');
        }

        // Track order
        function trackOrder(trackingNumber) {
            // This would typically open a tracking service
            alert(`Tracking number: ${trackingNumber}\n\nThis would open a tracking service in a real implementation.`);
        }

        // Reorder
        function reorder(orderId) {
            if (confirm('Add all items from this order to your cart?')) {
                // This would need to be implemented with AJAX
                alert('Reorder feature coming soon!');
            }
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed')) {
                e.target.classList.add('hidden');
            }
        });

        // Animate order cards
        if (window.anime) {
            anime({
                targets: '.order-card',
                opacity: [0, 1],
                translateY: [40, 0],
                delay: anime.stagger(100),
                duration: 700,
                easing: 'easeOutCubic'
            });
        }
    </script>

    <?php require_once '../../templates/footer.php'; ?>
</body>
</html> 