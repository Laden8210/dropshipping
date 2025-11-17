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
                   u.first_name, u.last_name, u.email AS user_email, u.phone_number,
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

        // get payment details if exists
        $sqlPayment = "SELECT * FROM order_payments WHERE order_id = ?";
        $stmt = $this->conn->prepare($sqlPayment);
        $stmt->bind_param("i", $order['order_id']);
        $stmt->execute();
        $paymentResult = $stmt->get_result();
        $order['payment'] = $paymentResult->fetch_assoc();
        $stmt->close();


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
                             ORDER BY osh.created_at ASC";
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
                    'tracking_number' => $row['tracking_number'] ?? null,
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

    public function getBySupplierId($supplier_id)
    {
        $sql = "SELECT 
                o.*, 
                u.first_name, u.last_name, u.email AS user_email,
                oi.order_item_id, oi.product_id, oi.quantity, oi.price, oi.variation_id,
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
            LEFT JOIN products p ON oi.product_id = p.product_id
            WHERE p.user_id = ?
            ORDER BY o.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $supplier_id);
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
                    'tracking_number' => $row['tracking_number'] ?? null,
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
                    'variation_id' => $row['variation_id'],
                ];
            }
        }

        return array_values($orders);
    }

    public function getByCourier()
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
            LEFT JOIN products p ON oi.product_id = p.product_id
            WHERE o.tracking_number IS NOT NULL
            ORDER BY o.created_at DESC";

        $stmt = $this->conn->prepare($sql);

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
                    'tracking_number' => $row['tracking_number'] ?? null,
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

    public function getByStoreId($store_id)
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
            WHERE o.store_id = ?
            ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $store_id);

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
                    'tracking_number' => $row['tracking_number'] ?? null,
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
                    'tracking_number' => $row['tracking_number'] ?? null,
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

    public function printAWB($tracking_number)
    {
        try {
            // Enhanced order query with additional fields
            $orderQuery = "
            SELECT 
                o.order_id,
                o.order_number,
                o.tracking_number,
                o.created_at,
                o.subtotal,
                o.shipping_fee,
                o.tax,
                o.total_amount,
                sp.store_id,
                sp.store_name,
                sp.store_address,
                sp.store_phone,
                sp.store_email,
                usa.address_line,
                usa.region,
                usa.city,
                usa.brgy,
                usa.postal_code,
                u.first_name,
                u.last_name,
                u.phone_number,
                u.email as customer_email,
                pm.payment_method,
                pm.amount as payment_amount,
                pm.status as payment_status,
                oss.status as current_status
            FROM orders o
            JOIN store_profile sp ON o.store_id = sp.store_id
            JOIN user_shipping_address usa ON o.shipping_address_id = usa.address_id
            JOIN users u ON o.user_id = u.user_id
            LEFT JOIN order_payments pm ON o.order_id = pm.order_id
            LEFT JOIN (
                SELECT osh1.order_id, osh1.status
                FROM order_status_history osh1
                INNER JOIN (
                    SELECT order_id, MAX(status_history_id) as max_id
                    FROM order_status_history
                    GROUP BY order_id
                ) osh2 ON osh1.order_id = osh2.order_id AND osh1.status_history_id = osh2.max_id
            ) oss ON o.order_id = oss.order_id
            WHERE o.tracking_number = ?
        ";

            $stmt = $this->conn->prepare($orderQuery);
            $stmt->bind_param("s", $tracking_number);
            $stmt->execute();
            $result = $stmt->get_result();
            $orderData = $result->fetch_assoc();
            $stmt->close();

            if (!$orderData) {
                throw new Exception("Order not found");
            }

            // FIXED: Corrected items query with proper joins
            $itemsQuery = "
            SELECT 
                p.product_name,
                p.product_sku,
                oi.quantity,
                oi.price,
                oi.variation_id,
                pvs.size,
                pvs.color,
                pvs.weight,
                pvs.length,
                pvs.width,
                pvs.height,
                ip.profit_margin
            FROM order_items oi
            JOIN imported_product ip ON oi.product_id = ip.imported_product_id  -- This is the correct join
            JOIN products p ON ip.product_id = p.product_id  -- Then join to products table
            LEFT JOIN product_variations_simple pvs ON oi.variation_id = pvs.variation_id
            WHERE oi.order_id = ?
        ";

            $stmt = $this->conn->prepare($itemsQuery);
            $stmt->bind_param("i", $orderData['order_id']);
            $stmt->execute();
            $itemsResult = $stmt->get_result();

            $orderItems = [];
            $totalWeight = 0;
            $totalItems = 0;

            while ($row = $itemsResult->fetch_assoc()) {
                $orderItems[] = $row;
                $totalItems += $row['quantity'];

                // Calculate weight if available
                if ($row['weight']) {
                    $totalWeight += ($row['weight'] * $row['quantity']);
                } else {
                    // Default weight if not specified
                    $totalWeight += (100 * $row['quantity']); // 100g default per item
                }
            }
            $stmt->close();

            // Debug: Check if items were found
            if (empty($orderItems)) {
                // Let's check what order_items actually exist for this order
                $debugQuery = "SELECT * FROM order_items WHERE order_id = ?";
                $stmt = $this->conn->prepare($debugQuery);
                $stmt->bind_param("i", $orderData['order_id']);
                $stmt->execute();
                $debugResult = $stmt->get_result();
                $debugItems = [];
                while ($row = $debugResult->fetch_assoc()) {
                    $debugItems[] = $row;
                }
                $stmt->close();

                // If debug items exist but our main query didn't work, there might be a data issue
                if (!empty($debugItems)) {
                    error_log("Debug: Found " . count($debugItems) . " order_items but main query returned empty. Order ID: " . $orderData['order_id']);

                    // Alternative query as fallback
                    $fallbackQuery = "
                SELECT 
                    oi.order_item_id,
                    oi.quantity,
                    oi.price,
                    oi.variation_id,
                    pvs.size,
                    pvs.color,
                    p.product_name,
                    p.product_sku,
                    oi.product_id
                FROM order_items as oi
                LEFT JOIN imported_product ip ON oi.product_id = ip.imported_product_id
                LEFT JOIN products p ON oi.product_id = p.product_id
                LEFT JOIN product_variations_simple pvs ON oi.variation_id = pvs.variation_id
                WHERE oi.order_id  = ?
                ";

                    $stmt = $this->conn->prepare($fallbackQuery);
                    $stmt->bind_param("i", $orderData['order_id']);
                    $stmt->execute();
                    $fallbackResult = $stmt->get_result();

                    while ($row = $fallbackResult->fetch_assoc()) {
                        $orderItems[] = $row;
                        
                        $totalItems += $row['quantity'];
                        $totalWeight += (100 * $row['quantity']); // Default weight
                    }
                    $stmt->close();
                }
            }

            // Get package dimensions (use largest item or calculate)
            $packageDimensions = $this->calculatePackageDimensions($orderItems);

            $order = [
                'order_number' => $orderData['order_number'],
                'tracking_number' => $orderData['tracking_number'],
                'order_date' => $orderData['created_at'],
                'current_status' => $orderData['current_status'] ?? 'pending',
                'financial_info' => [
                    'subtotal' => $orderData['subtotal'],
                    'shipping_fee' => $orderData['shipping_fee'],
                    'tax' => $orderData['tax'],
                    'total_amount' => $orderData['total_amount']
                ],
                'payment_info' => [
                    'method' => $orderData['payment_method'] ?? 'Not specified',
                    'amount' => $orderData['payment_amount'] ?? 0,
                    'status' => $orderData['payment_status'] ?? 'unknown'
                ],
                'shipping_address' => [
                    'first_name' => $orderData['first_name'],
                    'last_name' => $orderData['last_name'],
                    'phone_number' => $orderData['phone_number'],
                    'email' => $orderData['customer_email'],
                    'address_line' => $orderData['address_line'],
                    'brgy' => $orderData['brgy'],
                    'city' => $orderData['city'],
                    'region' => $orderData['region'],
                    'postal_code' => $orderData['postal_code'],
                    'full_address' => $this->formatFullAddress($orderData)
                ],
                'store_profile' => [
                    'store_id' => $orderData['store_id'],
                    'store_name' => $orderData['store_name'],
                    'store_address' => $orderData['store_address'],
                    'store_phone' => $orderData['store_phone'],
                    'store_email' => $orderData['store_email']
                ],
                'items' => $orderItems,
                'package_info' => [
                    'total_items' => $totalItems,
                    'total_weight' => $totalWeight > 0 ? round($totalWeight / 1000, 2) : 0.5, // Convert to kg, default 0.5kg
                    'dimensions' => $packageDimensions,
                    'package_count' => 1
                ],
                'service_type' => 'Standard Delivery'
            ];

            return $order;
        } catch (Exception $e) {
            throw new Exception("Error generating AWB: " . $e->getMessage());
        }
    }

    // Helper function to format full address
    private function formatFullAddress($orderData)
    {
        $addressParts = [
            $orderData['address_line'],
            $orderData['brgy'],
            $orderData['city'],
            $orderData['region'],
            $orderData['postal_code']
        ];

        return implode(', ', array_filter($addressParts));
    }

    // Helper function to calculate package dimensions
    private function calculatePackageDimensions($items)
    {
        if (empty($items)) {
            return [
                'length' => 15,
                'width' => 10,
                'height' => 5,
                'unit' => 'cm'
            ];
        }

        $maxLength = 0;
        $maxWidth = 0;
        $totalHeight = 0;

        foreach ($items as $item) {
            $itemLength = $item['length'] ?? 10;
            $itemWidth = $item['width'] ?? 8;
            $itemHeight = $item['height'] ?? 3;

            if ($itemLength > $maxLength) $maxLength = $itemLength;
            if ($itemWidth > $maxWidth) $maxWidth = $itemWidth;
            $totalHeight += ($itemHeight * $item['quantity']);
        }

        // Add some buffer for packaging
        $maxLength += 2;
        $maxWidth += 2;
        $totalHeight += 5;

        return [
            'length' => round($maxLength, 1),
            'width' => round($maxWidth, 1),
            'height' => round($totalHeight, 1),
            'unit' => 'cm'
        ];
    }
}
