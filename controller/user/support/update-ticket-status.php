<?php
// Admin API for Updating Ticket Status
// Allows admins to change ticket status (open, in_progress, resolved, closed)

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

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed. Use PUT.',
        'data' => null
    ]);
    exit;
}

try {
    // Get PUT data
    parse_str(file_get_contents('php://input'), $put_data);
    
    $ticket_id = $put_data['ticket_id'] ?? '';
    $status = $put_data['status'] ?? '';
    
    if (empty($ticket_id) || empty($status)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required fields: ticket_id, status',
            'data' => null
        ]);
        exit;
    }

    // Validate status
    if (!in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid status. Must be: open, in_progress, resolved, or closed',
            'data' => null
        ]);
        exit;
    }

    $user_store_id = $_SESSION['auth']['store_id'];

    // Verify ticket exists and belongs to user's store
    $ticket_check = $conn->prepare("SELECT ticket_id, status FROM support_tickets WHERE ticket_id = ? AND store_id = ?");
    $ticket_check->bind_param("si", $ticket_id, $user_store_id);
    $ticket_check->execute();
    $ticket_result = $ticket_check->get_result();

    if ($ticket_result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Ticket not found',
            'data' => null
        ]);
        exit;
    }

    $old_status = $ticket_result->fetch_assoc()['status'];

    // Update ticket status
    $update_query = "UPDATE support_tickets SET status = ?, updated_at = NOW()";
    
    // Set resolved_at timestamp if status is resolved or closed
    if ($status === 'resolved' || $status === 'closed') {
        $update_query .= ", resolved_at = NOW()";
    } else {
        $update_query .= ", resolved_at = NULL";
    }
    
    $update_query .= " WHERE ticket_id = ?";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ss", $status, $ticket_id);

    if ($stmt->execute()) {
        // Add system message about status change
        if ($old_status !== $status) {
            $message_id = UIDGenerator::generateMessageId();
            $system_message = "Ticket status changed from " . str_replace('_', ' ', $old_status) . " to " . str_replace('_', ' ', $status) . ".";
            
            $insert_message = "INSERT INTO support_messages (
                message_id, 
                ticket_id, 
                sender_id, 
                sender_type, 
                message, 
                message_type, 
                created_at
            ) VALUES (?, ?, ?, 'system', ?, 'text', NOW())";
            
            $msg_stmt = $conn->prepare($insert_message);
            $user_id = $_SESSION['auth']['user_id'];
            $msg_stmt->bind_param("ssss", $message_id, $ticket_id, $user_id, $system_message);
            $msg_stmt->execute();
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Ticket status updated successfully',
            'http_code' => 200,
            'data' => [
                'ticket_id' => $ticket_id,
                'old_status' => $old_status,
                'new_status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        throw new Exception('Failed to update ticket status: ' . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Admin update ticket status error: " . $e->getMessage());
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
