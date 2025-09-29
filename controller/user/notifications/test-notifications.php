<?php
header('Content-Type: application/json');
require_once '../../../core/config.php';
require_once '../../../services/NotificationService.php';

// Check user authentication


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

try {
    $test_email = $_POST['test_email'] ?? '';
    $test_phone = $_POST['test_phone'] ?? '';
    
    if (empty($test_email) && empty($test_phone)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Test email or phone number is required.'
        ]);
        exit;
    }

    // Initialize notification service
    $notificationService = new NotificationService();
    $results = [];

    // Test SMS if phone provided
    if ($test_phone) {
        $results['sms'] = $notificationService->sendSMS(
            $test_phone,
            'Test SMS from dropshipping system - Order notification service is working!'
        );
    }

    // Test Email if email provided
    if ($test_email) {
        $results['email'] = $notificationService->sendEmail(
            $test_email,
            'Test Email - Dropshipping Order Notifications',
            "Hello,\n\nThis is a test email to verify that the order notification service is working correctly.\n\nYou will receive notifications when your order status changes.\n\nThank you for choosing our service!\n\nBest regards,\nDropshipping Support Team",
            'Test User'
        );
    }

    // Check overall success
    $overall_success = true;
    foreach ($results as $result) {
        if (!$result['success']) {
            $overall_success = false;
            break;
        }
    }

    echo json_encode([
        'status' => $overall_success ? 'success' : 'partial',
        'message' => $overall_success ? 'All notifications sent successfully' : 'Some notifications failed',
        'data' => [
            'test_results' => $results,
            'overall_success' => $overall_success,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    error_log("Notification test error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) $conn->close();
}
?>
