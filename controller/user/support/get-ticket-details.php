<?php
// Admin API for Getting Ticket Details and Messages
// Returns detailed ticket information with conversation history

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
    $ticket_id = $_GET['ticket_id'] ?? '';
    
    if (empty($ticket_id)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required parameter: ticket_id',
            'data' => null
        ]);
        exit;
    }

    // Get user's store_id for access control
    $user_store_id = $_SESSION['auth']['store_id'];

    // Get ticket details - only for user's store
    $ticket_query = "
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
            u.first_name,
            u.last_name,
            u.email,
            u.phone_number,
            u.user_id
        FROM support_tickets t
        LEFT JOIN orders o ON t.order_id = o.order_id
        LEFT JOIN store_profile sp ON t.store_id = sp.store_id
        LEFT JOIN users u ON t.user_id = u.user_id
        WHERE t.ticket_id = ? AND t.store_id = ?
    ";

    $stmt = $conn->prepare($ticket_query);
    $stmt->bind_param("si", $ticket_id, $user_store_id);
    $stmt->execute();
    $ticket_result = $stmt->get_result();

    if ($ticket_result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Ticket not found',
            'data' => null
        ]);
        exit;
    }

    $ticket_data = $ticket_result->fetch_assoc();

    // Get messages for this ticket
    $messages_query = "
        SELECT 
            m.message_id,
            m.sender_id,
            m.sender_type,
            m.message,
            m.message_type,
            m.attachment_url,
            m.is_read,
            m.created_at,
            u.first_name,
            u.last_name
        FROM support_messages m
        LEFT JOIN users u ON m.sender_id = u.user_id
        WHERE m.ticket_id = ?
        ORDER BY m.created_at ASC
    ";

    $msg_stmt = $conn->prepare($messages_query);
    $msg_stmt->bind_param("s", $ticket_id);
    $msg_stmt->execute();
    $messages_result = $msg_stmt->get_result();

    $messages = [];
    while ($row = $messages_result->fetch_assoc()) {
        $messages[] = [
            'message_id' => $row['message_id'],
            'sender_id' => $row['sender_id'],
            'sender_type' => $row['sender_type'],
            'sender_name' => $row['sender_type'] === 'system' ? 'System' : ($row['first_name'] . ' ' . $row['last_name']),
            'message' => $row['message'],
            'message_type' => $row['message_type'],
            'attachment_url' => $row['attachment_url'],
            'is_read' => (bool)$row['is_read'],
            'created_at' => $row['created_at']
        ];
    }

    // Mark messages as read for user (store owner)
    $mark_read = "UPDATE support_messages SET is_read = TRUE WHERE ticket_id = ? AND sender_type = 'customer' AND is_read = FALSE";
    $read_stmt = $conn->prepare($mark_read);
    $read_stmt->bind_param("s", $ticket_id);
    $read_stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Ticket details retrieved successfully',
        'http_code' => 200,
        'data' => [
            'ticket' => [
                'user_id' => $ticket_data['user_id'],
                'ticket_id' => $ticket_data['ticket_id'],
                'order_id' => $ticket_data['order_id'],
                'order_number' => $ticket_data['order_number'],
                'subject' => $ticket_data['subject'],
                'description' => $ticket_data['description'],
                'priority' => $ticket_data['priority'],
                'status' => $ticket_data['status'],
                'category' => $ticket_data['category'],
                'assigned_to' => $ticket_data['assigned_to'],
                'store_name' => $ticket_data['store_name'],
                'order_amount' => $ticket_data['total_amount'],
                'customer_name' => $ticket_data['first_name'] . ' ' . $ticket_data['last_name'],
                'customer_email' => $ticket_data['email'],
                'customer_phone' => $ticket_data['phone_number'],
                'created_at' => $ticket_data['created_at'],
                'updated_at' => $ticket_data['updated_at'],
                'resolved_at' => $ticket_data['resolved_at']
            ],
            'messages' => $messages,
            'total_messages' => count($messages)
        ]
    ]);

} catch (Exception $e) {
    error_log("Admin get ticket details error: " . $e->getMessage());
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
