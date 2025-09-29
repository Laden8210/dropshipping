/**
 * Notification Helper Functions
 * Provides easy-to-use functions for sending order status notifications
 */

class NotificationHelper {
    
    /**
     * Send order status notification
     * 
     * @param {Object} params - Parameters object
     * @param {number} params.orderId - Order ID
     * @param {string} params.status - New order status
     * @param {string} params.customerPhone - Customer phone number (optional)
     * @param {string} params.customerEmail - Customer email (optional)
     * @param {string} params.customerName - Customer name (optional)
     * @param {string} params.orderNumber - Order number (optional)
     * @param {Function} callback - Callback function for response
     */
    static sendOrderStatusNotification(params, callback = null) {
        const { orderId, status, customerPhone, customerEmail, customerName, orderNumber } = params;
        
        if (!orderId || !status) {
            console.error('NotificationHelper: Order ID and status are required');
            if (callback) callback('Order ID and status are required', null);
            return;
        }

        const data = {
            order_id: orderId,
            status: status,
            ...(customerPhone && { customer_phone: customerPhone }),
            ...(customerEmail && { customer_email: customerEmail }),
            ...(customerName && { customer_name: customerName }),
            ...(orderNumber && { order_number: orderNumber })
        };

        fetch('/api/notifications/order-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (callback) {
                callback(null, result);
            }
            
            if (result.status === 'success') {
                console.log('Order notification sent successfully:', result.data);
                
                // Show success message
                Swal.fire({
                    title: 'Notifications Sent',
                    text: `Customer has been notified about order status change to: ${status}`,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                console.error('Order notification failed:', result.message);
                
                // Show error message
                Swal.fire({
                    title: 'Notification Failed',
                    text: result.message,
                    icon: 'error',
                    timer: 5000
                });
            }
        })
        .catch(error => {
            console.error('Order notification error:', error);
            if (callback) {
                callback(error.message, null);
            }
            
            Swal.fire({
                title: 'Network Error',
                text: 'Failed to send notification. Please try again.',
                icon: 'error',
                timer: 5000
            });
        });
    }

    /**
     * Test notification service
     * 
     * @param {string} testEmail - Test email address
     * @param {string} testPhone - Test phone number
     * @param {Function} callback - Callback function for response
     */
    static testNotifications(testEmail = '', testPhone = '', callback = null) {
        if (!testEmail && !testPhone) {
            console.error('NotificationHelper: Test email or phone number is required');
            if (callback) callback('Test email or phone number is required', null);
            return;
        }

        const formData = new FormData();
        if (testEmail) formData.append('test_email', testEmail);
        if (testPhone) formData.append('test_phone', testPhone);

        fetch('/controller/user/notifications/test-notifications.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (callback) {
                callback(null, result);
            }
            
            if (result.status === 'success') {
                console.log('Test notifications sent successfully');
                
                Swal.fire({
                    title: 'Test Successful',
                    text: 'Test notifications sent successfully!',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                console.error('Test notifications failed:', result.message);
                
                Swal.fire({
                    title: 'Test Failed',
                    text: result.message,
                    icon: 'error',
                    timer: 5000
                });
            }
        })
        .catch(error => {
            console.error('Test notification error:', error);
            if (callback) {
                callback(error.message, null);
            }
            
            Swal.fire({
                title: 'Test Error',
                text: 'Failed to send test notification. Please check your configuration.',
                icon: 'error',
                timer: 5000
            });
        });
    }

    /**
     * Send notification with order details from order object
     * 
     * @param {Object} order - Order object with customer details
     * @param {string} newStatus - New order status
     * @param {Function} callback - Callback function for response
     */
    static notifyOrderStatusChange(order, newStatus, callback = null) {
        this.sendOrderStatusNotification({
            orderId: order.order_id,
            status: newStatus,
            customerPhone: order.customer_phone || order.phone,
            customerEmail: order.customer_email || order.email,
            customerName: order.customer_name || `${order.first_name} ${order.last_name}`,
            orderNumber: order.order_number
        }, callback);
    }

    /**
     * Show notification test dialog
     */
    static showTestDialog() {
        Swal.fire({
            title: 'Test Notifications',
            html: `
                <div class="mb-3">
                    <label for="testEmail" class="form-label">Test Email</label>
                    <input type="email" id="testEmail" class="form-control" placeholder="test@example.com">
                </div>
                <div class="mb-3">
                    <label for="testPhone" class="form-label">Test Phone</label>
                    <input type="tel" id="testPhone" class="form-control" placeholder="+1234567890">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Send Test',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const testEmail = document.getElementById('testEmail').value.trim();
                const testPhone = document.getElementById('testPhone').value.trim();
                
                if (!testEmail && !testPhone) {
                    Swal.showValidationMessage('Please enter at least a test email or phone number');
                    return false;
                }
                
                return { testEmail, testPhone };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                NotificationHelper.testNotifications(result.value.testEmail, result.value.testPhone);
            }
        });
    }
}

// Make it globally available
window.NotificationHelper = NotificationHelper;

// Auto-initialize notification buttons if present on the page
document.addEventListener('DOMContentLoaded', function() {
    // Add notification buttons to order status change forms
    const statusUpdateForms = document.querySelectorAll('form[data-action="update-order-status"]');
    
    statusUpdateForms.forEach(form => {
        // Add notification checkbox if not already present
        if (!form.querySelector('input[name="send_notification"]')) {
            const notificationCheckbox = document.createElement('div');
            notificationCheckbox.className = 'form-check mb-3';
            notificationCheckbox.innerHTML = `
                <input class="form-check-input" type="checkbox" name="send_notification" id="sendNotification${form.dataset.orderId}" checked>
                <label class="form-check-label" for="sendNotification${form.dataset.orderId}">
                    Notify customer about status change
                </label>
            `;
            
            // Insert before submit button
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.parentNode.insertBefore(notificationCheckbox, submitButton);
            }
        }
    });
    
    // Handle form submission to include notification
    statusUpdateForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const sendNotification = form.querySelector('input[name="send_notification"]');
            if (sendNotification && sendNotification.checked) {
                // Store original form action
                const originalAction = form.action;
                
                // Temporarily change action to include notification
                form.action = '/controller/user/notifications/send-order-notification.php';
                
                // Add hidden field for notification
                const notificationInput = document.createElement('input');
                notificationInput.type = 'hidden';
                notificationInput.name = 'send_notification_only';
                notificationInput.value = 'true';
                form.appendChild(notificationInput);
            }
        });
    });
});
