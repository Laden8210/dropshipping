<?php
// Test endpoint for Gemini AI integration

require_once '../../core/config.php';
require_once '../../services/GeminiService.php';

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

try {
    // Get test data from request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $test_message = $input['message'] ?? 'Hello, I have a question about my order.';
    $test_ticket_id = $input['ticket_id'] ?? null;

    // Initialize Gemini service
    $geminiService = new GeminiService();
    
    // Test basic connectivity first
    $connection_test = $geminiService->testConnection();
    
    if (!$connection_test['success']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gemini AI connection failed: ' . $connection_test['error'],
            'data' => [
                'connection_test' => $connection_test
            ]
        ]);
        exit;
    }

    // If ticket_id provided, test with real ticket context
    if ($test_ticket_id) {
        // Get ticket and order details for context
        $context_query = "
            SELECT 
                t.ticket_id,
                t.subject,
                t.description,
                t.priority,
                t.status,
                t.category,
                t.created_at,
                o.order_id,
                o.order_number,
                o.total_amount,
                o.tracking_number,
                o.status as order_status,
                sp.store_name,
                u.first_name,
                u.last_name,
                u.email,
                u.phone
            FROM support_tickets t
            LEFT JOIN orders o ON t.order_id = o.order_id
            LEFT JOIN store_profile sp ON t.store_id = sp.store_id
            LEFT JOIN users u ON t.user_id = u.user_id
            WHERE t.ticket_id = ?
        ";
        
        $stmt = $conn->prepare($context_query);
        $stmt->bind_param("s", $test_ticket_id);
        $stmt->execute();
        $ticket_data = $stmt->get_result()->fetch_assoc();
        
        if ($ticket_data) {
            // Get message history
            $history_query = "
                SELECT sender_type, message, created_at
                FROM support_messages 
                WHERE ticket_id = ? 
                ORDER BY created_at ASC
                LIMIT 10
            ";
            
            $history_stmt = $conn->prepare($history_query);
            $history_stmt->bind_param("s", $test_ticket_id);
            $history_stmt->execute();
            $message_history = $history_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            // Combine data for context
            $ticket_data['messages'] = $message_history;
            $ticket_data['customer_name'] = $ticket_data['first_name'] . ' ' . $ticket_data['last_name'];
            $ticket_data['customer_email'] = $ticket_data['email'];
            $ticket_data['customer_phone'] = $ticket_data['phone'];
            
            // Generate AI response
            $ai_result = $geminiService->generateSupportResponse($ticket_data, $test_message);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Gemini AI test completed successfully',
                'data' => [
                    'connection_test' => $connection_test,
                    'test_message' => $test_message,
                    'ticket_context' => [
                        'ticket_id' => $ticket_data['ticket_id'],
                        'subject' => $ticket_data['subject'],
                        'order_number' => $ticket_data['order_number'],
                        'customer_name' => $ticket_data['customer_name'],
                        'message_count' => count($message_history)
                    ],
                    'ai_response' => $ai_result
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ticket not found',
                'data' => [
                    'ticket_id' => $test_ticket_id
                ]
            ]);
        }
        
        $stmt->close();
        
    } else {
        // Test with sample context
        $sample_context = [
            'ticket_id' => 'TKT-TEST-' . time(),
            'subject' => 'Order Inquiry Test',
            'status' => 'open',
            'priority' => 'medium',
            'category' => 'order_issue',
            'created_at' => date('Y-m-d H:i:s'),
            'order_id' => 12345,
            'order_number' => 'ORDER-123456',
            'total_amount' => 99.99,
            'tracking_number' => 'TRK123456789',
            'order_status' => 'shipped',
            'store_name' => 'Test Store',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+1234567890',
            'messages' => [
                [
                    'sender_type' => 'customer',
                    'message' => 'I ordered a product last week but haven\'t received any updates.',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ],
                [
                    'sender_type' => 'agent',
                    'message' => 'Thank you for contacting us. Let me check your order status.',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ]
            ]
        ];
        
        $ai_result = $geminiService->generateSupportResponse($sample_context, $test_message);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Gemini AI test completed with sample data',
            'data' => [
                'connection_test' => $connection_test,
                'test_message' => $test_message,
                'sample_context' => $sample_context,
                'ai_response' => $ai_result
            ]
        ]);
    }

} catch (Exception $e) {
    error_log("Gemini test error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>
