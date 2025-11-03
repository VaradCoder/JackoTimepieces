<?php
session_start();
require_once '../../core/db/connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_user'])) {
    header('Location: ../login.php');
    exit;
}

$message = '';

// Handle ticket status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ticket'])) {
    $ticket_id = intval($_POST['ticket_id']);
    $new_status = $_POST['new_status'];
    $priority = $_POST['priority'];
    $assigned_to = intval($_POST['assigned_to']);
    
    $stmt = $conn->prepare("UPDATE support_tickets SET status = ?, priority = ?, assigned_to = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("ssii", $new_status, $priority, $assigned_to, $ticket_id);
    
    if ($stmt->execute()) {
        $message = 'Ticket updated successfully!';
    } else {
        $message = 'Failed to update ticket.';
    }
}

// Handle ticket reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_ticket'])) {
    $ticket_id = intval($_POST['ticket_id']);
    $message_text = $_POST['message'];
    $admin_id = $_SESSION['admin_user']['id'];
    
    $stmt = $conn->prepare("INSERT INTO support_messages (ticket_id, admin_id, message, is_admin_reply) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("iis", $ticket_id, $admin_id, $message_text);
    
    if ($stmt->execute()) {
        // Update ticket status to in_progress if it was open
        $stmt = $conn->prepare("UPDATE support_tickets SET status = 'in_progress' WHERE id = ? AND status = 'open'");
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        
        $message = 'Reply sent successfully!';
    } else {
        $message = 'Failed to send reply.';
    }
}

// Get filters
$status_filter = $_GET['status'] ?? '';
$priority_filter = $_GET['priority'] ?? '';
$assigned_filter = $_GET['assigned'] ?? '';

// Build query
$where_conditions = [];
$params = [];
$param_types = '';

if ($status_filter) {
    $where_conditions[] = "st.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if ($priority_filter) {
    $where_conditions[] = "st.priority = ?";
    $params[] = $priority_filter;
    $param_types .= 's';
}

if ($assigned_filter) {
    $where_conditions[] = "st.assigned_to = ?";
    $params[] = $assigned_filter;
    $param_types .= 'i';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get tickets
$query = "
    SELECT st.*, u.first_name, u.last_name, u.email,
           au.first_name as admin_first_name, au.last_name as admin_last_name,
           COUNT(sm.id) as message_count
    FROM support_tickets st
    LEFT JOIN users u ON st.user_id = u.id
    LEFT JOIN admin_users au ON st.assigned_to = au.id
    LEFT JOIN support_messages sm ON st.id = sm.ticket_id
    $where_clause
    GROUP BY st.id
    ORDER BY 
        CASE st.priority 
            WHEN 'urgent' THEN 1 
            WHEN 'high' THEN 2 
            WHEN 'medium' THEN 3 
            WHEN 'low' THEN 4 
        END,
        st.created_at DESC
";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get admin users for assignment
$stmt = $conn->prepare("SELECT id, first_name, last_name FROM admin_users WHERE is_active = 1");
$stmt->execute();
$admin_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Function to get status color
function getStatusColor($status) {
    switch ($status) {
        case 'open':
            return 'bg-red-900 text-red-200';
        case 'in_progress':
            return 'bg-yellow-900 text-yellow-200';
        case 'resolved':
            return 'bg-green-900 text-green-200';
        case 'closed':
            return 'bg-gray-900 text-gray-200';
        default:
            return 'bg-gray-900 text-gray-200';
    }
}

// Function to get priority color
function getPriorityColor($priority) {
    switch ($priority) {
        case 'urgent':
            return 'bg-red-600 text-white';
        case 'high':
            return 'bg-orange-600 text-white';
        case 'medium':
            return 'bg-yellow-600 text-black';
        case 'low':
            return 'bg-green-600 text-white';
        default:
            return 'bg-gray-600 text-white';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets - Admin | JackoTimespiece</title>
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
                    <h1 class="text-3xl font-bold text-white">Support Tickets</h1>
                    <p class="text-gray-400">Manage customer support requests</p>
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
                        <option value="open" <?php echo $status_filter === 'open' ? 'selected' : ''; ?>>Open</option>
                        <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="resolved" <?php echo $status_filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Priority</label>
                    <select name="priority" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                        <option value="">All Priority</option>
                        <option value="urgent" <?php echo $priority_filter === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                        <option value="high" <?php echo $priority_filter === 'high' ? 'selected' : ''; ?>>High</option>
                        <option value="medium" <?php echo $priority_filter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="low" <?php echo $priority_filter === 'low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Assigned To</label>
                    <select name="assigned" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                        <option value="">All Admins</option>
                        <?php foreach ($admin_users as $admin): ?>
                            <option value="<?php echo $admin['id']; ?>" <?php echo $assigned_filter == $admin['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gold text-black px-4 py-2 rounded-lg hover:bg-white hover:text-gold transition">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="bg-gray-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Ticket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Assigned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        <?php foreach ($tickets as $ticket): ?>
                            <tr class="hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">#<?php echo $ticket['id']; ?></div>
                                    <div class="text-sm text-gray-400"><?php echo $ticket['message_count']; ?> messages</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">
                                        <?php echo htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-400"><?php echo htmlspecialchars($ticket['email']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($ticket['subject']); ?></div>
                                    <div class="text-sm text-gray-400 max-w-xs truncate"><?php echo htmlspecialchars(substr($ticket['message'], 0, 100)); ?>...</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo getStatusColor($ticket['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo getPriorityColor($ticket['priority']); ?>">
                                        <?php echo ucfirst($ticket['priority']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">
                                        <?php if ($ticket['assigned_to']): ?>
                                            <?php echo htmlspecialchars($ticket['admin_first_name'] . ' ' . $ticket['admin_last_name']); ?>
                                        <?php else: ?>
                                            <span class="text-gray-500">Unassigned</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <?php echo date('M j, Y', strtotime($ticket['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewTicket(<?php echo $ticket['id']; ?>)" 
                                                class="text-blue-400 hover:text-blue-300">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editTicket(<?php echo $ticket['id']; ?>)" 
                                                class="text-gold hover:text-yellow-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="replyTicket(<?php echo $ticket['id']; ?>)" 
                                                class="text-green-400 hover:text-green-300">
                                            <i class="fas fa-reply"></i>
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
                Showing <?php echo count($tickets); ?> tickets
            </div>
        </div>
    </div>

    <!-- Ticket Details Modal -->
    <div id="ticket-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-white">Ticket Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="ticket-details-content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Ticket Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Edit Ticket</h3>
                    <form id="edit-form" method="POST">
                        <input type="hidden" name="ticket_id" id="edit-ticket-id">
                        <input type="hidden" name="update_ticket" value="1">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                            <select name="new_status" id="edit-status" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white">
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Priority</label>
                            <select name="priority" id="edit-priority" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white">
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Assign To</label>
                            <select name="assigned_to" id="edit-assigned" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white">
                                <option value="">Unassigned</option>
                                <?php foreach ($admin_users as $admin): ?>
                                    <option value="<?php echo $admin['id']; ?>">
                                        <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-gold text-black px-4 py-2 rounded-lg hover:bg-white hover:text-gold transition">
                                Update Ticket
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

    <!-- Reply Modal -->
    <div id="reply-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg max-w-2xl w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Reply to Ticket</h3>
                    <form id="reply-form" method="POST">
                        <input type="hidden" name="ticket_id" id="reply-ticket-id">
                        <input type="hidden" name="reply_ticket" value="1">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Your Reply</label>
                            <textarea name="message" id="reply-message" rows="6" required
                                      class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white"
                                      placeholder="Enter your reply to the customer..."></textarea>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-gold text-black px-4 py-2 rounded-lg hover:bg-white hover:text-gold transition">
                                Send Reply
                            </button>
                            <button type="button" onclick="closeReplyModal()" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // View ticket details
        function viewTicket(ticketId) {
            // This would typically load ticket details via AJAX
            document.getElementById('ticket-details-content').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-info-circle text-gold text-4xl mb-4"></i>
                    <p class="text-gray-400">Detailed ticket information would be loaded here.</p>
                    <p class="text-gray-500 text-sm mt-2">This feature will show complete ticket details, messages, etc.</p>
                </div>
            `;
            document.getElementById('ticket-modal').classList.remove('hidden');
        }

        // Edit ticket
        function editTicket(ticketId) {
            document.getElementById('edit-ticket-id').value = ticketId;
            document.getElementById('edit-modal').classList.remove('hidden');
        }

        // Reply to ticket
        function replyTicket(ticketId) {
            document.getElementById('reply-ticket-id').value = ticketId;
            document.getElementById('reply-modal').classList.remove('hidden');
        }

        // Close modals
        function closeModal() {
            document.getElementById('ticket-modal').classList.add('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        function closeReplyModal() {
            document.getElementById('reply-modal').classList.add('hidden');
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