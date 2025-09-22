<?php
// Mobile API for Sending Chat Messages
// Allows customers and agents to send messages in support tickets

require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';
require_once '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
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
    $request_body = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
        exit;
    }

    // Get required fields
    $ticket_id = $request_body['ticket_id'] ?? '';
    $message = $request_body['message'] ?? '';
    $message_type = $request_body['message_type'] ?? 'text';
    $attachment_url = $request_body['attachment_url'] ?? null;

    // Validate required fields
    if (empty($ticket_id) || empty($message)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required fields: ticket_id, message',
            'data' => null
        ]);
        exit;
    }

    // Validate message type
    if (!in_array($message_type, ['text', 'image', 'file'])) {
        $message_type = 'text';
    }

    // Verify ticket exists and user has access
    $ticket_check = $conn->prepare("
        SELECT t.ticket_id, t.user_id, t.status, t.store_id
        FROM support_tickets t 
        WHERE t.ticket_id = ? AND (t.user_id = ? OR t.assigned_to = ?)
    ");
    $ticket_check->bind_param("sss", $ticket_id, $user_id, $user_id);
    $ticket_check->execute();
    $ticket_result = $ticket_check->get_result();

    if ($ticket_result->num_rows === 0) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'Ticket not found or access denied',
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

    // Determine sender type
    $sender_type = ($ticket_data['user_id'] === $user_id) ? 'customer' : 'agent';

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
        attachment_url, 
        is_read, 
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, FALSE, NOW())";

    $stmt = $conn->prepare($insert_message);
    $stmt->bind_param("sssssss", $message_id, $ticket_id, $user_id, $sender_type, $message, $message_type, $attachment_url);

    if ($stmt->execute()) {
        // Update ticket status if it was resolved
        if ($ticket_data['status'] === 'resolved') {
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
            'message' => 'Message sent successfully',
            'data' => [
                'message_id' => $message_id,
                'ticket_id' => $ticket_id,
                'sender_id' => $user_id,
                'sender_type' => $sender_type,
                'message' => $message,
                'message_type' => $message_type,
                'attachment_url' => $attachment_url,
                'is_read' => false,
                'created_at' => $message_data['created_at']
            ]
        ]);
    } else {
        throw new Exception('Failed to send message: ' . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Mobile message sending error: " . $e->getMessage());
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
