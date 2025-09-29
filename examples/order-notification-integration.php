<?php
/**
 * Example: Order Notification Integration
 * 
 * This example shows how to integrate notifications when order status changes
 * You can integrate this into your existing order management system
 */

require_once '../core/config.php';
require_once '../services/NotificationService.php';

// Example function to update order status and send notification
function updateOrderStatusWithNotification($order_id, $new_status, $send_notification = true) {
    global $conn;
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // 1. Update order status in database
        $update_query = "UPDATE orders SET status = ?, updated_at = NOW() WHERE order_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Order not found or status unchanged");
        }
        
        $stmt->close();
        
        // 2. Get order and customer details for notification
        $details_query = "
            SELECT 
                o.order_id,
                o.order_number,
                o.status,
                u.first_name,
                u.last_name,
                u.email,
                u.phone
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            WHERE o.order_id = ?
        ";
        
        $stmt = $conn->prepare($details_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Order details not found");
        }
        
        $order = $result->fetch_assoc();
        $stmt->close();
        
        // 3. Send notification if requested
        if ($send_notification) {
            $notificationService = new NotificationService();
            
            $notification_result = $notificationService->sendOrderStatusNotification(
                $order_id,
                $new_status,
                $order['phone'],
                $order['email'],
                $order['first_name'] . ' ' . $order['last_name']
            );
            
            // Log notification result
            if (!$notification_result['overall_success']) {
                error_log("Notification failed for order {$order_id}: " . json_encode($notification_result));
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        return [
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => [
                'order_id' => $order_id,
                'order_number' => $order['order_number'],
                'new_status' => $new_status,
                'customer_name' => $order['first_name'] . ' ' . $order['last_name'],
                'notification_sent' => $send_notification
            ]
        ];
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        
        error_log("Order status update error: " . $e->getMessage());
        
        return [
            'success' => false,
            'message' => 'Failed to update order status: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ];
    } finally {
        if (isset($stmt)) $stmt->close();
    }
}

// Example usage:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $new_status = $_POST['status'] ?? '';
    $send_notification = isset($_POST['send_notification']);
    
    if (empty($order_id) || empty($new_status)) {
        echo json_encode([
            'success' => false,
            'message' => 'Order ID and status are required'
        ]);
        exit;
    }
    
    $result = updateOrderStatusWithNotification($order_id, $new_status, $send_notification);
    echo json_encode($result);
    exit;
}

// HTML form example
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update with Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Update Order Status</h3>
                    </div>
                    <div class="card-body">
                        <form id="orderStatusForm">
                            <div class="mb-3">
                                <label for="orderId" class="form-label">Order ID</label>
                                <input type="number" class="form-control" id="orderId" name="order_id" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">New Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="processing">Processing</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="sendNotification" name="send_notification" checked>
                                <label class="form-check-label" for="sendNotification">
                                    Send notification to customer
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </form>
                        
                        <div id="result" class="mt-3"></div>
                    </div>
                </div>
                
                <!-- Test Notification Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Test Notifications</h5>
                    </div>
                    <div class="card-body">
                        <p>Test SMS and email notifications to verify your configuration.</p>
                        
                        <div class="mb-3">
                            <label for="testEmail" class="form-label">Test Email</label>
                            <input type="email" class="form-control" id="testEmail" placeholder="test@example.com">
                        </div>
                        
                        <div class="mb-3">
                            <label for="testPhone" class="form-label">Test Phone</label>
                            <input type="tel" class="form-control" id="testPhone" placeholder="+1234567890">
                        </div>
                        
                        <button type="button" class="btn btn-success" onclick="testNotifications()">
                            Send Test Notifications
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include notification helper -->
    <script src="../assets/js/notification-helper.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Handle form submission
    document.getElementById('orderStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
       const formData = new FormData(this);
        
        // Show loading
        Swal.fire({
            title: 'Updating...',
            text: 'Please wait while we update the order status',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            Swal.close();
            
            if (result.success) {
                Swal.fire({
                    title: 'Success!',
                    text: result.message,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
                
                // Clear form
                document.getElementById('orderStatusForm').reset();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.message,
                    icon: 'error',
                    timer: 5000
                });
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire({
                title: 'Network Error!',
                text: 'Failed to update order status. Please try again.',
                icon: 'error',
                timer: 5000
            });
        });
    });
    
    // Test notifications function
    function testNotifications() {
        const testEmail = document.getElementById('testEmail').value.trim();
        const testPhone = document.getElementById('testPhone').value.trim();
        
        if (!testEmail && !testPhone) {
            Swal.fire({
                title: 'Input Required',
                text: 'Please enter at least a test email or phone number',
                icon: 'warning'
});


            return;
        }
        
        NotificationHelper.testNotifications(testEmail, testPhone);
    }
    </script>
</body>
</html>
