<?php
// Mobile API for Getting User's Support Tickets
// Retrieves all support tickets for the authenticated user

require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed. Use POST.',
        'data' => null
    ]);
    exit;
}

// Authenticate user via JWT token
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Authorization token missing']);
    exit;
}
$jwt = $matches[1];

$secret_key = "dropshipping_8210";
try {
    $decoded = JWT::decode($jwt, new Key(trim($secret_key), 'HS256'));
    $user_id = $decoded->sub;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid token: ' . $e->getMessage()]);
    exit;
}

try {
    // Get optional filters
    $status = $_GET['status'] ?? '';
    $priority = $_GET['priority'] ?? '';
    $category = $_GET['category'] ?? '';

    // Build query with filters
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
            COUNT(m.message_id) as message_count,
            MAX(m.created_at) as last_message_at
        FROM support_tickets t
        LEFT JOIN orders o ON t.order_id = o.order_id
        LEFT JOIN store_profile sp ON t.store_id = sp.store_id
        LEFT JOIN support_messages m ON t.ticket_id = m.ticket_id
        WHERE t.user_id = ?
    ";

    $params = [$user_id];
    $param_types = "s";

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
            'order_amount' => $row['total_amount'],
            'message_count' => (int)$row['message_count'],
            'last_message_at' => $row['last_message_at'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'resolved_at' => $row['resolved_at']
        ];
    }

    // Get summary statistics
    $stats_query = "
        SELECT 
            COUNT(*) as total_tickets,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_tickets,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tickets,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_tickets,
            SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_tickets
        FROM support_tickets 
        WHERE user_id = ?
    ";
    
    $stats_stmt = $conn->prepare($stats_query);
    $stats_stmt->bind_param("s", $user_id);
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result()->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'message' => 'Tickets retrieved successfully',
        'data' => [
            'tickets' => $tickets,
            'summary' => [
                'total_tickets' => (int)$stats_result['total_tickets'],
                'open_tickets' => (int)$stats_result['open_tickets'],
                'in_progress_tickets' => (int)$stats_result['in_progress_tickets'],
                'resolved_tickets' => (int)$stats_result['resolved_tickets'],
                'closed_tickets' => (int)$stats_result['closed_tickets']
            ]
        ]
    ]);

} catch (Exception $e) {
    error_log("Mobile get tickets error: " . $e->getMessage());
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
