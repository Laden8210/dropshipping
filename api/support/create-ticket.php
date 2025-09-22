<?php
// Mobile API for Creating Support Tickets
// Allows mobile users to create support tickets related to their orders

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
    $order_id = $request_body['order_id'] ?? '';
    $subject = $request_body['subject'] ?? '';
    $description = $request_body['description'] ?? '';
    $priority = $request_body['priority'] ?? 'medium';
    $category = $request_body['category'] ?? 'other';

    // Validate required fields
    if (empty($order_id) || empty($subject) || empty($description)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required fields: order_id, subject, description',
            'data' => null
        ]);
        exit;
    }

    // Validate priority
    if (!in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
        $priority = 'medium';
    }

    // Validate category
    if (!in_array($category, ['order_issue', 'product_question', 'shipping', 'payment', 'technical', 'other'])) {
        $category = 'other';
    }

    // Verify order exists and belongs to user
    $order_check = $conn->prepare("
        SELECT o.order_id, o.store_id, o.order_number, o.total_amount, o.created_at as order_date
        FROM orders o 
        WHERE o.order_id = ? AND o.user_id = ?
    ");
    $order_check->bind_param("ss", $order_id, $user_id);
    $order_check->execute();
    $order_result = $order_check->get_result();

    if ($order_result->num_rows === 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Order not found or does not belong to you',
            'data' => null
        ]);
        exit;
    }

    $order_data = $order_result->fetch_assoc();
    $store_id = $order_data['store_id'];

    // Generate unique ticket ID
    $ticket_id = UIDGenerator::generateTicketId();

    // Create ticket
    $insert_ticket = "INSERT INTO support_tickets (
        ticket_id, 
        user_id, 
        order_id, 
        store_id, 
        subject, 
        description, 
        priority, 
        category, 
        status, 
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'open', NOW())";

    $stmt = $conn->prepare($insert_ticket);
    $stmt->bind_param("sssissss", $ticket_id, $user_id, $order_id, $store_id, $subject, $description, $priority, $category);

    if ($stmt->execute()) {
        // Create initial system message
        $message_id = UIDGenerator::generateMessageId();
        $system_message = "Support ticket created successfully. Our team will review your request and respond shortly.";

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
        $msg_stmt->bind_param("ssss", $message_id, $ticket_id, $user_id, $system_message);
        $msg_stmt->execute();

        // Get ticket details for response
        $ticket_query = "SELECT * FROM support_tickets WHERE ticket_id = ?";
        $ticket_stmt = $conn->prepare($ticket_query);
        $ticket_stmt->bind_param("s", $ticket_id);
        $ticket_stmt->execute();
        $ticket_data = $ticket_stmt->get_result()->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'message' => 'Support ticket created successfully',
            'data' => [
                'ticket_id' => $ticket_id,
                'order_id' => $order_id,
                'order_number' => $order_data['order_number'],
                'subject' => $subject,
                'description' => $description,
                'priority' => $priority,
                'category' => $category,
                'status' => 'open',
                'created_at' => $ticket_data['created_at'],
                'chat_available' => true
            ]
        ]);
    } else {
        throw new Exception('Failed to create ticket: ' . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Mobile ticket creation error: " . $e->getMessage());
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
