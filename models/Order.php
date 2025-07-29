<?php
require_once 'OrderStatusHistory.php';
class Order
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createOrder($user_id, $orderData)
    {
        $this->conn->begin_transaction();

        try {
            if (empty($orderData['products'])) {
                throw new Exception("Order must contain at least one product.");
            }

            // Group products by store
            $productsByStore = [];
            foreach ($orderData['products'] as $product) {
                $productsByStore[$product['store_id']][] = $product;
            }

            $orderResponses = [];

            foreach ($productsByStore as $store_id => $products) {
                // Recalculate amounts per store
                $subtotal = 0;
                foreach ($products as $product) {
                    $subtotal += $product['price'] * $product['quantity'];
                }

                $shipping = $orderData['shipping'] ?? 0;
                $tax = $orderData['tax'] ?? 0;
                $total = $subtotal + $shipping + $tax;

                $order_number = UIDGenerator::generateOrderNumber();

                // Insert order
                $stmt = $this->conn->prepare("
                INSERT INTO orders (
                    user_id, subtotal, shipping_fee, tax, total_amount,
                    order_number, shipping_address_id, store_id
                ) VALUES (?, ?,  ?, ?, ?, ?, ?, ?)
            ");

                $stmt->bind_param(
                    "sddddsii",
                    $user_id,
                    $subtotal,
                    $shipping,
                    $tax,
                    $total,
                    $order_number,
                    $orderData['shipping_address_id'],
                    $store_id
                );

                if (!$stmt->execute()) {
                    throw new Exception("Failed to create order for store $store_id: " . $stmt->error);
                }

                $order_id = $this->conn->insert_id;

                // Insert order items for this store
                foreach ($products as $product) {
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
                        throw new Exception("Failed to insert item for store $store_id: " . $stmt->error);
                    }
                }

            
                $stmt = $this->conn->prepare("
                    INSERT INTO order_payments (
                        order_id, payment_method, amount, status, transaction_id
                    ) VALUES (?, ?, ?, ?, ?)
                ");


                $transaction_id = UIDGenerator::generateTransactionId(); 
                $paymentMethod = $orderData['payment_method'] ?? 'unknown';
                $paymentStatus = 'pending';

                $stmt->bind_param(
                    "isdss",
                    $order_id,
                    $paymentMethod,
                    $total,
                    $paymentStatus,
                    $transaction_id
                );

                if (!$stmt->execute()) {
                    throw new Exception("Failed to create payment record for order $order_id: " . $stmt->error);
                }

                $orderStatusHistory = new OrderStatusHistory($this->conn);
                if (!$orderStatusHistory->create($order_id, 'pending')) {
                    throw new Exception("Failed to create order status for store $store_id.");
                }

                $orderResponses[] = [
                    'store_id' => $store_id,
                    'order_id' => $order_id,
                    'order_number' => $order_number
                ];
            }

            $this->conn->commit();

            return [
                'status' => 'success',
                'orders' => $orderResponses
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

        $this->conn->begin_transaction();

        try {
            // 1. Get order_id by order_number
            $sql = "SELECT order_id FROM orders WHERE order_number = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $order_number);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();

            if (!$order) {
                return [
                    'status' => 'error',
                    'message' => 'Order not found.'
                ];
            }

            $order_id = $order['order_id'];

            $insertHistory = "INSERT INTO order_status_history (order_id, status) VALUES (?, ?)";
            $stmt = $this->conn->prepare($insertHistory);
            $stmt->bind_param("is", $order_id, $status);
            $stmt->execute();


            $this->conn->commit();

            return [
                'status' => 'success',
                'message' => 'Order status updated successfully'
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'status' => 'error',
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ];
        }
    }

    public function getOrderHistoryStatus($order_number)
    {

        $sql = "SELECT osh.status, osh.created_at 
                FROM order_status_history osh
                JOIN orders o ON osh.order_id = o.order_id
                WHERE o.order_number = ?
                ORDER BY osh.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $order_number);
        $stmt->execute();
        $result = $stmt->get_result();

        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        return $history;
    }

    public function addTrackingNumber($order_number, $tracking_number)
    {

        $sql = "UPDATE orders SET tracking_number = ? WHERE order_number = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $tracking_number, $order_number);
        if (!$stmt->execute()) {
            return [
                'status' => 'error',
                'message' => 'Failed to add tracking number: ' . $stmt->error
            ];
        }
        return [
            'status' => 'success',
            'message' => 'Tracking number added successfully'
        ];
    }

}
