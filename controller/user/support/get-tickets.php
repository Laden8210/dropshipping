<?php
// Admin API for Getting All Support Tickets
// Returns tickets with filtering and summary statistics

require_once '../../../core/config.php';
require_once '../../../core/request.php';

header('Content-Type: application/json');

// Check user authentication
session_start();
if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Forbidden: User access required.'
    ]);
    exit;
}

try {
    // Get optional filters
    $status = $_GET['status'] ?? '';
    $priority = $_GET['priority'] ?? '';
    $category = $_GET['category'] ?? '';
    $store_id = $_GET['store_id'] ?? '';

    // Get user's store_id for filtering
    $user_store_id = $_SESSION['auth']['store_id'];
    
    // Build query with filters - only show tickets for user's store
    $query = "
        SELECT 
            t.ticket_id,
            t.order_id,
            t.subject,
            t.description,
            t.priority,
            t.status,
            t.category,
            t.assigned_to,
            t.created_at,
            t.updated_at,
            t.resolved_at,
            o.order_number,
            o.total_amount,
            sp.store_name,
            sp.store_id,
            u.first_name,
            u.last_name,
            u.email,
            COUNT(m.message_id) as message_count,
            MAX(m.created_at) as last_message_at
        FROM support_tickets t
        LEFT JOIN orders o ON t.order_id = o.order_id
        LEFT JOIN store_profile sp ON t.store_id = sp.store_id
        LEFT JOIN users u ON t.user_id = u.user_id
        LEFT JOIN support_messages m ON t.ticket_id = m.ticket_id
        WHERE t.store_id = ?
    ";

    $params = [$user_store_id];
    $param_types = "i";

    if (!empty($status)) {
        $query .= " AND t.status = ?";
        $params[] = $status;
        $param_types .= "s";
    }

    if (!empty($priority)) {
        $query .= " AND t.priority = ?";
        $params[] = $priority;
        $param_types .= "s";
    }

    if (!empty($category)) {
        $query .= " AND t.category = ?";
        $params[] = $category;
        $param_types .= "s";
    }


    $query .= " GROUP BY t.ticket_id ORDER BY t.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $tickets = [];
    while ($row = $result->fetch_assoc()) {
        $tickets[] = [
            'ticket_id' => $row['ticket_id'],
            'order_id' => $row['order_id'],
            'order_number' => $row['order_number'],
            'subject' => $row['subject'],
            'description' => $row['description'],
            'priority' => $row['priority'],
            'status' => $row['status'],
            'category' => $row['category'],
            'assigned_to' => $row['assigned_to'],
            'store_name' => $row['store_name'],
            'store_id' => $row['store_id'],
            'customer_name' => $row['first_name'] . ' ' . $row['last_name'],
            'customer_email' => $row['email'],
            'order_amount' => $row['total_amount'],
            'message_count' => (int)$row['message_count'],
            'last_message_at' => $row['last_message_at'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'resolved_at' => $row['resolved_at']
        ];
    }

    // Get summary statistics for user's store only
    $stats_query = "
        SELECT 
            COUNT(*) as total_tickets,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_tickets,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tickets,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_tickets,
            SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_tickets,
            AVG(CASE WHEN resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) END) as avg_resolution_time
        FROM support_tickets
        WHERE store_id = ?
    ";
    
    $stats_stmt = $conn->prepare($stats_query);
    $stats_stmt->bind_param("i", $user_store_id);
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result();
    $stats = $stats_result->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'message' => 'Tickets retrieved successfully',
        'http_code' => 200,
        'data' => [
            'tickets' => $tickets,
            'summary' => [
                'total_tickets' => (int)$stats['total_tickets'],
                'open_tickets' => (int)$stats['open_tickets'],
                'in_progress_tickets' => (int)$stats['in_progress_tickets'],
                'resolved_tickets' => (int)$stats['resolved_tickets'],
                'closed_tickets' => (int)$stats['closed_tickets'],
                'avg_resolution_time' => round($stats['avg_resolution_time'] ?? 0, 2)
            ]
        ]
    ]);

} catch (Exception $e) {
    error_log("Admin get tickets error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
