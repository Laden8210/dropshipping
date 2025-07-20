<?php
require_once 'OrderStatusHistory.php';
class Order
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createOrder($user_id, $order_number, $tracking_number, $orderData)
    {
        $this->conn->begin_transaction();

        try {

            $stmt = $this->conn->prepare("
                INSERT INTO orders (
                    user_id, subtotal, shipping_fee, tax, total_amount,
                    order_number, payment_method, shipping_address_id, tracking_number
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sddddssss",
                $user_id,
                $orderData['subtotal'],
                $orderData['shipping'],
                $orderData['tax'],
                $orderData['total'],
                $order_number,
                $orderData['payment_method'],
                $orderData['shipping_address_id'],
                $tracking_number
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to create order.");
            }

            $order_id = $this->conn->insert_id;

            // Insert order items
            foreach ($orderData['products'] as $product) {
                $stmt = $this->conn->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->bind_param(
                    "iiid",
                    $order_id,
                    $product['pid'],
                    $product['quantity'],
                    $product['price']
                );

                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert order item.");
                }
            }

            $orderStatusHistory = new OrderStatusHistory($this->conn);
            if (!$orderStatusHistory->create($order_id, 'pending')) {
                throw new Exception("Failed to create order status history.");
            }

            $this->conn->commit();

            return [
                'status' => 'success',
                'order_id' => $order_id,
                'order_number' => $order_number
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getOrderBy($order_number)
    {
 
        $sql = "
        SELECT 
            o.order_id,
            o.user_id,
            o.subtotal,
            o.shipping_fee,
            o.tax,
            o.total_amount,
            o.status,
            o.payment_method,
            o.order_number,
            o.created_at
        FROM orders o
        WHERE o.order_number = ?
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $order_number);
        $stmt->execute();
        $orderResult = $stmt->get_result();

        if ($orderResult->num_rows === 0) {
            return ['status' => 'error', 'message' => 'Order not found'];
        }

        $order = $orderResult->fetch_assoc();


        $itemsSql = "
        SELECT 
            oi.product_id,
            oi.quantity,
            oi.price
        FROM order_items oi
        JOIN imported_product ip ON oi.product_id = ip.product_id
        WHERE oi.order_id = ?
    ";
    

        $stmtItems = $this->conn->prepare($itemsSql);
        $stmtItems->bind_param("i", $order['order_id']);
        $stmtItems->execute();
        $itemsResult = $stmtItems->get_result();

        $items = [];
        while ($row = $itemsResult->fetch_assoc()) {
            $items[] = $row;
        }

        $order['products'] = $items;

        return [
            'status' => 'success',
            'data' => $order
        ];
    }

    public function updateOrderStatus($order_number, $status)
    {
        $sql = "UPDATE orders SET status = ? WHERE order_number = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $status, $order_number);

        if (!$stmt->execute()) {
            return [
                'status' => 'error',
                'message' => 'Failed to update order status'
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Order status updated successfully'
        ];
    }
}
