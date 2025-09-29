<?php
// Mobile API for Getting Chat Messages
// Retrieves conversation history for a support ticket

require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
        'message' => 'Method not allowed. Use GET.',
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
    // Get ticket_id from query parameters
    $request_body = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
        exit;
    }

    $ticket_id =  $request_body['ticket_id'] ?? '';
    
    if (empty($ticket_id)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required parameter: ticket_id',
            'data' => null
        ]);
        exit;
    }

    // Verify ticket exists and user has access
    $ticket_check = $conn->prepare("
        SELECT t.ticket_id, t.user_id, t.subject, t.status, t.priority, t.category, t.created_at as ticket_created
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
            m.created_at
        FROM support_messages m
        WHERE m.ticket_id = ?
        ORDER BY m.created_at ASC
    ";

    $stmt = $conn->prepare($messages_query);
    $stmt->bind_param("s", $ticket_id);
    $stmt->execute();
    $messages_result = $stmt->get_result();

    $messages = [];
    while ($row = $messages_result->fetch_assoc()) {
        $messages[] = [
            'message_id' => $row['message_id'],
            'sender_id' => $row['sender_id'],
            'sender_type' => $row['sender_type'],
            'message' => $row['message'],
            'message_type' => $row['message_type'],
            'attachment_url' => $row['attachment_url'],
            'is_read' => (bool)$row['is_read'],
            'created_at' => $row['created_at']
        ];
    }

    // Mark messages as read for the current user
    $mark_read = "UPDATE support_messages SET is_read = TRUE WHERE ticket_id = ? AND sender_id != ? AND is_read = FALSE";
    $read_stmt = $conn->prepare($mark_read);
    $read_stmt->bind_param("ss", $ticket_id, $user_id);
    $read_stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Messages retrieved successfully',
        'data' => [
            'ticket' => [
                'ticket_id' => $ticket_data['ticket_id'],
                'subject' => $ticket_data['subject'],
                'status' => $ticket_data['status'],
                'priority' => $ticket_data['priority'],
                'category' => $ticket_data['category'],
                'created_at' => $ticket_data['ticket_created']
            ],
            'messages' => $messages,
            'total_messages' => count($messages)
        ]
    ]);

} catch (Exception $e) {
    error_log("Mobile get messages error: " . $e->getMessage());
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
