<?php
// Mobile API for Sending Chat Messages
// Allows customers and agents to send messages in support tickets

require_once '../../core/config.php';
require_once '../../models/index.php';
require_once '../../function/UIDGenerator.php';
require_once '../../services/GeminiService.php';
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

        // Generate AI response if message is from customer
        $ai_response = null;

        // Only generate AI response for customer messages
        if ($sender_type === 'customer') {
            try {
                // Get complete ticket and order details for Gemini context
                $context_query = "
                    SELECT 
                        t.ticket_id,
                        t.subject,
                        t.description,
                        t.status as ticket_status,
                        t.created_at,
                        t.order_id,
                        o.order_number,
                        o.total_amount,
                        o.tracking_number,
                        osh.status as order_status,
                        sp.store_name,
                        u.first_name,
                        u.last_name,
                        u.email,
                        u.phone_number
                    FROM support_tickets t
                    LEFT JOIN orders o ON t.order_id = o.order_id
                    LEFT JOIN store_profile sp ON o.store_id = sp.store_id
                    LEFT JOIN users u ON t.user_id = u.user_id
                    LEFT JOIN (
                        SELECT order_id, status
                        FROM order_status_history
                        WHERE (order_id, status_history_id) IN (
                            SELECT order_id, MAX(status_history_id)
                            FROM order_status_history
                            GROUP BY order_id
                        )
                    ) osh ON o.order_id = osh.order_id
                    WHERE t.ticket_id = ?
                ";

                $context_stmt = $conn->prepare($context_query);
                $context_stmt->bind_param("s", $ticket_id);
                $context_stmt->execute();
                $ticket_context = $context_stmt->get_result()->fetch_assoc();

                // Get conversation history
                $history_query = "
                    SELECT sender_type, message, created_at
                    FROM support_messages 
                    WHERE ticket_id = ? 
                    ORDER BY created_at ASC
                ";

                $history_stmt = $conn->prepare($history_query);
                $history_stmt->bind_param("s", $ticket_id);
                $history_stmt->execute();
                $message_history = $history_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                // Prepare context for Gemini
                $gemini_context = [
                    'ticket_id' => $ticket_context['ticket_id'] ?? '',
                    'subject' => $ticket_context['subject'] ?? '',
                    'description' => $ticket_context['description'] ?? '',
                    'ticket_status' => $ticket_context['ticket_status'] ?? '',
                    'created_at' => $ticket_context['created_at'] ?? '',
                    'order_id' => $ticket_context['order_id'] ?? '',
                    'order_number' => $ticket_context['order_number'] ?? '',
                    'total_amount' => $ticket_context['total_amount'] ?? '',
                    'tracking_number' => $ticket_context['tracking_number'] ?? '',
                    'order_status' => $ticket_context['order_status'] ?? '',
                    'store_name' => $ticket_context['store_name'] ?? '',
                    'customer_name' => trim(($ticket_context['first_name'] ?? '') . ' ' . ($ticket_context['last_name'] ?? '')),
                    'customer_email' => $ticket_context['email'] ?? '',
                    'customer_phone' => $ticket_context['phone_number'] ?? '',
                    'messages' => $message_history
                ];

                // Initialize Gemini service and generate response
                $geminiService = new GeminiService();
                $ai_result = $geminiService->generateSupportResponse($gemini_context, $message);

                // Save AI response to database if successful
                if ($ai_result['success']) {
                    $ai_message_id = UIDGenerator::generateMessageId();
                    $ai_message_text = $ai_result['message'];

                    $insert_ai_message = "INSERT INTO support_messages (
                            message_id, 
                            ticket_id, 
                            sender_id, 
                            sender_type, 
                            message, 
                            message_type, 
                            attachment_url, 
                            is_read, 
                            created_at
                        ) VALUES (?, ?, ?, 'system', ?, 'text', NULL, FALSE, NOW())";

                    $ai_stmt = $conn->prepare($insert_ai_message);
                    $ai_stmt->bind_param("ssss", $ai_message_id, $ticket_id, $user_id, $ai_message_text);
                    $ai_stmt->execute();

                    $ai_response = [
                        'message_id' => $ai_message_id,
                        'sender_type' => 'system',
                        'message' => $ai_message_text,
                        'message_type' => 'text',
                        'is_read' => false,
                        'created_at' => date('Y-m-d H:i:s'),
                        'metadata' => $ai_result['metadata'] ?? null
                    ];
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'AI response generated successfully',
                        'data' => $ai_response
                    ]);
                    exit;
                    // Log successful AI response
                    error_log("AI response generated for ticket {$ticket_id}: " . substr($ai_message_text, 0, 100) . "...");
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Gemini AI integration error: ' . $ai_result['error'],
                        'data' => null
                    ]);
                    exit;
                    error_log("AI response generation failed for ticket {$ticket_id}: " . ($ai_result['error'] ?? 'Unknown error'));
                }

                // Close statements
                $context_stmt->close();
                $history_stmt->close();
                if (isset($ai_stmt)) {
                    $ai_stmt->close();
                }
            } catch (Exception $e) {
                // Log AI error but don't fail the entire request
                error_log("Gemini AI integration error: " . $e->getMessage());
                // Continue with normal response without AI
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gemini AI integration error: ' . $e->getMessage(),
                    'data' => null
                ]);
                exit;
            }
        }

        // Return success response
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
                'created_at' => $message_data['created_at'],
                'ai_response' => $ai_response // Include AI response if generated
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