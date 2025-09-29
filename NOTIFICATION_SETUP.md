# Notification Service Setup Guide

This guide explains how to configure the SMS and Email notification services for order status updates.

## Environment Variables Required

Create or update your `.env` file with the following variables:

### Database Configuration (already configured)
```
DB_HOST=localhost
DB_USER=your_db_username
DB_PASS=your_db_password
DB_USER=dropshipping
```

### SMS Configuration (TextBee)
```
TEXTBEE_API_KEY=your_textbee_api_key_here
TEXTBEE_DEVICE_ID=your_device_id_here
```

### Email Configuration (SMTP)
```
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_FROM=dropshipping@yourapp.com
```

### Optional Testing
```
TEST_EMAIL=test@yourdomain.com
```

## SMS Setup (TextBee)

1. Sign up at [app.textbee.dev](https://app.textbee.dev)
2. Create a device/phone number
3. Get your API key and device ID from the dashboard
4. Add the credentials to your `.env` file

### TextBee API Usage
- API URL: `https://api.textbee.dev/api/v1/gateway/devices/{DEVICE_ID}/send-sms`
- Headers: `Content-Type: application/json`, `x-api-key: YOUR_API_KEY`
- Body: `{"recipients": ["+1234567890"], "message": "Hello!"}`

## Email Setup (SMTP)

### Gmail SMTP Configuration
```php
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password  // Use App Password, not regular password
SMTP_FROM=dropshipping@yourapp.com
```

### Other SMTP Providers

#### Outlook/Hotmail
```
SMTP_HOST=smtp-mail.outlook.com
SMTP_PORT=587
```

#### Yahoo
```
SMTP_HOST=smtp.mail.yahoo.com
SMTP_PORT=587
```

## Usage Examples

### 1. Direct API Call for Order Status Notification
```javascript
// Send notification when order status changes
fetch('/api/notifications/order-status.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        order_id: 123,
        status: 'shipped',
        customer_phone': '+1234567890',
        customer_email': 'customer@example.com',
        customer_name': 'John Doe',
        order_number': 'ORDER-000123'
    })
});
```

### 2. PHP Integration in Order Management
```php
<?php
require_once 'services/NotificationService.php';

$notification = new NotificationService();
$result = $notification->sendOrderStatusNotification(
    $order_id,
    $new_status,
    $customer_phone,
    $customer_email,
    $customer_name
);
?>
```

### 3. Test Notifications
```javascript
// Test endpoint
fetch('/controller/user/notifications/test-notifications.php', {
    method: 'POST',
    body: new FormData({
        test_email: 'test@example.com',
        test_phone: '+1234567890'
    })
});
```

## Order Status Messages

The system automatically generates appropriate messages for each status:

- **pending**: "Pending Payment"
- **confirmed**: "Confirmed - Your order has been confirmed"
- **processing**: "Processing - Your order is being prepared"
- **shipped**: "Shipped - Your order is on its way"
- **delivered**: "Delivered - Your order has been delivered"
- **cancelled**: "Cancelled - Your order has been cancelled"
- **refunded**: "Refunded - A refund has been processed"

## API Endpoints

### Order Status Notification
- **URL**: `/api/notifications/order-status.php`
- **Method**: POST
- **Purpose**: Send notifications for order status changes

### Test Notifications
- **URL**: `/controller/user/notifications/test-notifications.php`
- **Method**: POST
- **Purpose**: Test SMS and email connectivity

## Error Handling

All notification methods return structured responses:

```json
{
    "success": true/false,
    "message": "Human readable message",
    "data": {...},
    "error": "Error details if failed"
}
```

## Testing

1. Configure your `.env` file with valid credentials
2. Use the test endpoint to verify connectivity
3. Check server logs for any errors
4. Test with real order status changes

## Security Notes

- Store credentials securely in `.env` file
- Use app passwords for Gmail (not regular passwords)
- Validate SMS phone numbers before sending
- Rate limit notification sending if needed
- Log all notification attempts for debugging
