<?php
class OrderProduct
{
    private $conn;
    private $orderTable = "orders";
    private $orderItemTable = "order_items";
    private $userTable = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function get($orderId)
    {
        $sql = "SELECT o.*, 
                       u.first_name, u.last_name, u.email AS user_email
                FROM {$this->orderTable} o
                LEFT JOIN {$this->userTable} u ON o.user_id = u.user_id
                WHERE o.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Get order items
        $sqlItems = "SELECT * FROM {$this->orderItemTable} WHERE order_id = ?";
        $stmt = $this->conn->prepare($sqlItems);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $itemsResult = $stmt->get_result();
        $items = [];

        while ($item = $itemsResult->fetch_assoc()) {
            $items[] = $item;
        }

        $order['items'] = $items;
        return $order;
    }


    public function getByOrderNumber($orderNumber)
    {
        $sql = "SELECT o.*, 
                   u.first_name, u.last_name, u.email AS user_email,
                   usa.address_line, usa.region, usa.city, usa.brgy, usa.postal_code
            FROM {$this->orderTable} o
            LEFT JOIN {$this->userTable} u ON o.user_id = u.user_id
            LEFT JOIN user_shipping_address usa ON o.shipping_address_id = usa.address_id
            WHERE o.order_number = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $orderNumber);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$order) {
            return null;
        }

        $sqlItems = "SELECT oi.*, 
                        p.product_name, p.product_sku, p.product_category, p.description, 
                        pi.image_url AS primary_image, 
                        pc.category_name
                 FROM {$this->orderItemTable} oi
                 LEFT JOIN products p ON oi.product_id = p.product_id
                 LEFT JOIN (
                     SELECT pi1.product_id, pi1.image_url
                     FROM product_images pi1
                     WHERE pi1.is_primary = 1
                     AND pi1.image_id = (
                         SELECT MIN(pi2.image_id)
                         FROM product_images pi2
                         WHERE pi2.product_id = pi1.product_id AND pi2.is_primary = 1
                     )
                 ) pi ON p.product_id = pi.product_id
                 LEFT JOIN product_categories pc ON p.product_category = pc.category_id
                 WHERE oi.order_id = ?";

        $stmt = $this->conn->prepare($sqlItems);
        $stmt->bind_param("i", $order['order_id']);
        $stmt->execute();
        $itemsResult = $stmt->get_result();
        $items = [];

        while ($item = $itemsResult->fetch_assoc()) {
            $items[] = $item;
        }

        $order['items'] = $items;

        if (!empty($order['address_line'])) {
            $order['shipping_address'] = [
                'address_line' => $order['address_line'],
                'region'       => $order['region'],
                'city'         => $order['city'],
                'brgy'         => $order['brgy'],
                'postal_code'  => $order['postal_code'],
            ];
        } else {
            $order['shipping_address'] = null;
        }

        // Get order status history sql
        $sqlStatusHistory = "SELECT osh.status, osh.created_at 
                             FROM order_status_history osh 
                             WHERE osh.order_id = ? 
                             ORDER BY osh.created_at DESC";
        $stmt = $this->conn->prepare($sqlStatusHistory);
        $stmt->bind_param("i", $order['order_id']);
        $stmt->execute();
        $statusResult = $stmt->get_result();
        $order['status_history'] = [];
        while ($status = $statusResult->fetch_assoc()) {
            $order['status_history'][] = [
                'status' => $status['status'],
                'created_at' => $status['created_at']
            ];
        }
        if (!empty($order['status_history'])) {
            $latestStatus = end($order['status_history']);
            $order['status'] = $latestStatus['status'];
        } else {
            $order['status'] = 'pending';
        }


        unset($order['address_line'], $order['region'], $order['city'], $order['brgy'], $order['postal_code']);

        return $order;
    }

    public function getOrderDetails($orderId, $userId)
    {
        $sql = "SELECT o.*, 
                       u.first_name, u.last_name, u.email AS user_email,
                       usa.address_line, usa.region, usa.city, usa.brgy, usa.postal_code
                FROM {$this->orderTable} o
                LEFT JOIN {$this->userTable} u ON o.user_id = u.user_id
                LEFT JOIN user_shipping_address usa ON o.shipping_address_id = usa.address_id
                WHERE o.order_id = ? AND o.user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $orderId, $userId);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$order) {
            return null;
        }

        $sqlItems = "SELECT oi.*, 
                            p.product_name,
                            pi.image_url AS primary_image,
                            p.product_sku,
                            p.product_category, p.description,
                            pc.category_name
                     FROM {$this->orderItemTable} oi
                     LEFT JOIN products p ON oi.product_id = p.product_id
                     LEFT JOIN product_categories pc ON p.product_category = pc.category_id
                     LEFT JOIN (
                         SELECT pi1.product_id, pi1.image_url
                         FROM product_images pi1
                         WHERE pi1.is_primary = 1
                         AND pi1.image_id = (
                             SELECT MIN(pi2.image_id)
                             FROM product_images pi2
                             WHERE pi2.product_id = pi1.product_id AND pi2.is_primary = 1
                         )
                     ) pi ON p.product_id = pi.product_id
                     WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($sqlItems);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $itemsResult = $stmt->get_result();
        $items = [];
        while ($item = $itemsResult->fetch_assoc()) {
            $items[] = $item;
        }
        $stmt->close();
        $order['items'] = $items;

        // Get order status history sql
        $sqlStatusHistory = "SELECT osh.status, osh.created_at 
                             FROM order_status_history osh 
                             WHERE osh.order_id = ? 
                             ORDER BY osh.created_at DESC";
        $stmt = $this->conn->prepare($sqlStatusHistory);
        $stmt->bind_param("i", $order['order_id']);
        $stmt->execute();
        $statusResult = $stmt->get_result();
        $order['status_history'] = [];
        while ($status = $statusResult->fetch_assoc()) {
            $order['status_history'][] = [
                'status' => $status['status'],
                'created_at' => $status['created_at']
            ];
        }
        $stmt->close();
        if (!empty($order['status_history'])) {
            $latestStatus = end($order['status_history']);
            $order['status'] = $latestStatus['status'];
        } else {
            $order['status'] = 'pending';
        }

        if (!empty($order['address_line'])) {
            $order['shipping_address'] = [
                'address_line' => $order['address_line'],
                'region'       => $order['region'],
                'city'         => $order['city'],
                'brgy'         => $order['brgy'],
                'postal_code'  => $order['postal_code'],
            ];
        } else {
            $order['shipping_address'] = null;
        }

        unset($order['address_line'], $order['region'], $order['city'], $order['brgy'], $order['postal_code']);

        return $order;
    }



    public function getItems($order_id)
    {
        $sql = "SELECT * FROM {$this->orderItemTable} WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }


    public function getAll()
    {
        $sql = "SELECT 
                o.*, 
                u.first_name, u.last_name, u.email AS user_email,
                oi.order_item_id, oi.product_id, oi.quantity, oi.price,
                osh.status AS latest_status
            FROM {$this->orderTable} o
            LEFT JOIN {$this->userTable} u ON o.user_id = u.user_id
            LEFT JOIN {$this->orderItemTable} oi ON o.order_id = oi.order_id
            LEFT JOIN (
                SELECT osh1.order_id, osh1.status
                FROM order_status_history osh1
                INNER JOIN (
                    SELECT order_id, MAX(created_at) AS latest_created
                    FROM order_status_history
                    GROUP BY order_id
                ) osh2 ON osh1.order_id = osh2.order_id AND osh1.created_at = osh2.latest_created
            ) osh ON o.order_id = osh.order_id
            ORDER BY o.created_at DESC";

        $result = $this->conn->query($sql);
        $orders = [];

        while ($row = $result->fetch_assoc()) {
            $oid = $row['order_id'];

            if (!isset($orders[$oid])) {
                $orders[$oid] = [
                    'order_id' => $row['order_id'],
                    'user_id' => $row['user_id'],
                    'order_number' => $row['order_number'],
                    'total_amount' => $row['total_amount'],
                    'created_at' => $row['created_at'],
                    'status' => $row['latest_status'] ?? '',
                    'user' => [
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'],
                        'email' => $row['user_email'],
                    ],
                    'items' => []
                ];
            }

            if (!empty($row['order_item_id'])) {
                $orders[$oid]['items'][] = [
                    'order_item_id' => $row['order_item_id'],
                    'product_id' => $row['product_id'],
                    'quantity' => $row['quantity'],
                    'price' => $row['price'],
                ];
            }
        }

        return array_values($orders);
    }

    public function getAllOrderOfUser($userId)
    {
        $sql = "SELECT 
            o.*, 
            u.first_name, u.last_name, u.email AS user_email,
            oi.order_item_id, oi.product_id, oi.quantity, oi.price,
            p.product_name,
            osh.status AS latest_status
        FROM {$this->orderTable} o
        LEFT JOIN {$this->userTable} u ON o.user_id = u.user_id
        LEFT JOIN {$this->orderItemTable} oi ON o.order_id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.product_id
        LEFT JOIN (
            SELECT osh1.order_id, osh1.status
            FROM order_status_history osh1
            INNER JOIN (
                SELECT order_id, MAX(created_at) AS latest_created
                FROM order_status_history
                GROUP BY order_id
            ) osh2 ON osh1.order_id = osh2.order_id AND osh1.created_at = osh2.latest_created
        ) osh ON o.order_id = osh.order_id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";


        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];

        while ($row = $result->fetch_assoc()) {
            $oid = $row['order_id'];

            if (!isset($orders[$oid])) {
                $orders[$oid] = [
                    'order_id' => $row['order_id'],
                    'user_id' => $row['user_id'],
                    'order_number' => $row['order_number'],
                    'total_amount' => $row['total_amount'],
                    'created_at' => $row['created_at'],
                    'status' => $row['latest_status'] ?? '',
                    'user' => [
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'],
                        'email' => $row['user_email'],
                    ],
                    'items' => []
                ];
            }

            if (!empty($row['order_item_id'])) {
                $orders[$oid]['items'][] = [
                    'order_item_id' => $row['order_item_id'],
                    'product_id' => $row['product_id'],
                    'quantity' => $row['quantity'],
                    'price' => $row['price'],
                    'product_name' => $row['product_name'] ?? '',
                ];
            }
        }

        return array_values($orders);
    }


    public function is_order_exist($orderNumber)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->orderTable} WHERE order_number = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $orderNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    public function is_order_cancelled($orderNumber)
    {
        $sql = "SELECT COUNT(*) as count FROM order_status_history WHERE order_id = (SELECT order_id FROM {$this->orderTable} WHERE order_number = ?) AND status = 'cancelled'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $orderNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    public function cancel_order($orderNumber)
    {
        $this->conn->begin_transaction();

        try {
            // Step 1: Get the order_id by order_number
            $sql = "SELECT order_id FROM {$this->orderTable} WHERE order_number = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $orderNumber);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Order not found.");
            }

            $order = $result->fetch_assoc();
            $orderId = $order['order_id'];

            // Optional: Check latest status to prevent double cancellation
            $sql = "SELECT status FROM order_status_history WHERE order_id = ? ORDER BY created_at DESC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $statusResult = $stmt->get_result();
            if ($statusResult->num_rows > 0) {
                $latestStatus = $statusResult->fetch_assoc()['status'];
                if ($latestStatus === 'cancelled') {
                    throw new Exception("Order already cancelled.");
                }
            }

            // Step 2: Insert into order_status_history
            $sql = "INSERT INTO order_status_history (order_id, status) VALUES (?, 'cancelled')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();

 
            // $sql = "UPDATE order_payments SET status = 'refunded' WHERE order_id = ?";
            // $stmt = $this->conn->prepare($sql);
            // $stmt->bind_param("i", $orderId);
            // $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Cancel order error: " . $e->getMessage());
            return $e->getMessage(); // return error message string
        }
    }

    public function update($orderId, $data)
    {
        $this->conn->begin_transaction();

        try {

            $sql = "UPDATE {$this->orderTable} SET remark = ?, updated_at = CURRENT_TIMESTAMP WHERE order_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $data['remark'], $orderId);
            $stmt->execute();


            if (!empty($data['status'])) {
                $sql = "INSERT INTO order_status_history (order_id, status) VALUES (?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("is", $orderId, $data['status']);
                $stmt->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Update order error: " . $e->getMessage());
            return false;
        }
    }


    // Delete order and related items
    public function delete($orderId)
    {
        $this->conn->begin_transaction();

        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->orderItemTable} WHERE order_id = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->conn->prepare("DELETE FROM {$this->orderTable} WHERE order_id = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
