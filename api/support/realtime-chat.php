<?php
// Real-time Chat API for Support Tickets
// Handles WebSocket-like functionality for real-time messaging

require_once '../../core/config.php';
require_once '../../function/UIDGenerator.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check authentication
session_start();
$user_id = $_SESSION['auth']['user_id'] ?? '';
$user_role = $_SESSION['auth']['role'] ?? '';

if (empty($user_id)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
    exit;
}

try {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'check_messages':
            checkNewMessages();
            break;
        case 'mark_read':
            markMessagesAsRead();
            break;
        case 'get_online_status':
            getOnlineStatus();
            break;
        case 'update_status':
            updateUserStatus();
            break;
        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    error_log("Real-time chat error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}

function checkNewMessages() {
    global $conn, $user_id, $user_role;
    
    $ticket_id = $_GET['ticket_id'] ?? '';
    $last_message_id = $_GET['last_message_id'] ?? '';
    
    if (empty($ticket_id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Ticket ID required']);
        return;
    }
    
    // Verify user has access to ticket
    $access_query = "
        SELECT ticket_id FROM support_tickets 
        WHERE ticket_id = ? AND (user_id = ? OR assigned_to = ? OR ? = 'admin')
    ";
    $stmt = $conn->prepare($access_query);
    $stmt->bind_param("ssss", $ticket_id, $user_id, $user_id, $user_role);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
        return;
    }
    
    // Get new messages
    $query = "
        SELECT 
            m.message_id,
            m.sender_id,
            m.sender_type,
            m.message,
            m.message_type,
            m.attachment_url,
            m.created_at,
            u.first_name,
            u.last_name
        FROM support_messages m
        LEFT JOIN users u ON m.sender_id = u.user_id
        WHERE m.ticket_id = ?
    ";
    
    if (!empty($last_message_id)) {
        $query .= " AND m.message_id > ?";
    }
    
    $query .= " ORDER BY m.created_at ASC";
    
    $stmt = $conn->prepare($query);
    if (!empty($last_message_id)) {
        $stmt->bind_param("ss", $ticket_id, $last_message_id);
    } else {
        $stmt->bind_param("s", $ticket_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'message_id' => $row['message_id'],
            'sender_id' => $row['sender_id'],
            'sender_type' => $row['sender_type'],
            'sender_name' => $row['sender_type'] === 'system' ? 'System' : ($row['first_name'] . ' ' . $row['last_name']),
            'message' => $row['message'],
            'message_type' => $row['message_type'],
            'attachment_url' => $row['attachment_url'],
            'created_at' => $row['created_at']
        ];
    }
    
    // Mark messages as read for current user
    if (!empty($messages)) {
        $mark_read = "UPDATE support_messages SET is_read = TRUE WHERE ticket_id = ? AND sender_id != ? AND is_read = FALSE";
        $read_stmt = $conn->prepare($mark_read);
        $read_stmt->bind_param("ss", $ticket_id, $user_id);
        $read_stmt->execute();
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'messages' => $messages,
            'count' => count($messages),
            'timestamp' => time()
        ]
    ]);
}

function markMessagesAsRead() {
    global $conn, $user_id;
    
    $ticket_id = $_POST['ticket_id'] ?? '';
    
    if (empty($ticket_id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Ticket ID required']);
        return;
    }
    
    $mark_read = "UPDATE support_messages SET is_read = TRUE WHERE ticket_id = ? AND sender_id != ? AND is_read = FALSE";
    $stmt = $conn->prepare($mark_read);
    $stmt->bind_param("ss", $ticket_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Messages marked as read']);
    } else {
        throw new Exception('Failed to mark messages as read');
    }
}

function getOnlineStatus() {
    global $conn;
    
    $ticket_id = $_GET['ticket_id'] ?? '';
    
    if (empty($ticket_id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Ticket ID required']);
        return;
    }
    
    // Get ticket participants
    $participants_query = "
        SELECT DISTINCT 
            CASE 
                WHEN t.user_id = ? THEN t.assigned_to
                ELSE t.user_id
            END as other_user_id,
            u.first_name,
            u.last_name,
            u.last_activity
        FROM support_tickets t
        LEFT JOIN users u ON (
            CASE 
                WHEN t.user_id = ? THEN t.assigned_to
                ELSE t.user_id
            END = u.user_id
        )
        WHERE t.ticket_id = ?
    ";
    
    $stmt = $conn->prepare($participants_query);
    $stmt->bind_param("sss", $user_id, $user_id, $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $participants = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['other_user_id']) {
            $last_activity = strtotime($row['last_activity']);
            $is_online = (time() - $last_activity) < 300; // 5 minutes
            
            $participants[] = [
                'user_id' => $row['other_user_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'is_online' => $is_online,
                'last_activity' => $row['last_activity']
            ];
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'participants' => $participants,
            'timestamp' => time()
        ]
    ]);
}

function updateUserStatus() {
    global $conn, $user_id;
    
    $status = $_POST['status'] ?? 'online';
    
    $update_query = "UPDATE users SET last_activity = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("s", $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Status updated']);
    } else {
        throw new Exception('Failed to update user status');
    }
}

$conn->close();
?>
