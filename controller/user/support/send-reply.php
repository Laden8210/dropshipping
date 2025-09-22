<?php
// Admin API for Sending Reply to Support Ticket
// Allows admins to respond to customer support tickets

require_once '../../../core/config.php';
require_once '../../../core/request.php';
require_once '../../../function/UIDGenerator.php';

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed. Use POST.',
        'data' => null
    ]);
    exit;
}

try {
    $ticket_id = $_POST['ticket_id'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($ticket_id) || empty($message)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required fields: ticket_id, message',
            'data' => null
        ]);
        exit;
    }

    $user_id = $_SESSION['auth']['user_id'];
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

    $ticket_data = $ticket_result->fetch_assoc();

    // Check if ticket is closed
    if ($ticket_data['status'] === 'closed') {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Cannot send messages to closed tickets',
            'data' => null
        ]);
        exit;
    }

    // Generate unique message ID
        $message_id = UIDGenerator::generateMessageId();

    // Insert message
    $insert_message = "INSERT INTO support_messages (
        message_id, 
        ticket_id, 
        sender_id, 
        sender_type, 
        message, 
        message_type, 
        is_read, 
        created_at
    ) VALUES (?, ?, ?, 'agent', ?, 'text', FALSE, NOW())";

    $stmt = $conn->prepare($insert_message);
    $stmt->bind_param("ssss", $message_id, $ticket_id, $user_id, $message);

    if ($stmt->execute()) {
        // Update ticket status if it was open
        if ($ticket_data['status'] === 'open') {
            $update_ticket = "UPDATE support_tickets SET status = 'in_progress', updated_at = NOW() WHERE ticket_id = ?";
            $update_stmt = $conn->prepare($update_ticket);
            $update_stmt->bind_param("s", $ticket_id);
            $update_stmt->execute();
        }

        // Get message details for response
        $message_query = "SELECT * FROM support_messages WHERE message_id = ?";
        $msg_stmt = $conn->prepare($message_query);
        $msg_stmt->bind_param("s", $message_id);
        $msg_stmt->execute();
        $message_data = $msg_stmt->get_result()->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'message' => 'Reply sent successfully',
            'http_code' => 200,
            'data' => [
                'message_id' => $message_id,
                'ticket_id' => $ticket_id,
                'sender_id' => $user_id,
                'sender_type' => 'agent',
                'message' => $message,
                'message_type' => 'text',
                'is_read' => false,
                'created_at' => $message_data['created_at']
            ]
        ]);
    } else {
        throw new Exception('Failed to send reply: ' . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Admin send reply error: " . $e->getMessage());
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
