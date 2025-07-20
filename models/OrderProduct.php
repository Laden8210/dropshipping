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


    public function create($data, $items = [])
    {
        $this->conn->begin_transaction();

        try {
            $sql = "INSERT INTO {$this->orderTable} (
                user_id, total_amount, status, order_number, shipping_zip, shipping_country, shipping_country_code,
                shipping_province, shipping_city, shipping_county, shipping_phone, shipping_customer_name,
                shipping_address, shipping_address2, tax_id, remark, email, consignee_id, pay_type, shop_amount,
                logistic_name, from_country_code, house_number
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "sdsssssssssssssssssdsss",
                $data['user_id'], $data['total_amount'], $data['status'], $data['order_number'],
                $data['shipping_zip'], $data['shipping_country'], $data['shipping_country_code'],
                $data['shipping_province'], $data['shipping_city'], $data['shipping_county'],
                $data['shipping_phone'], $data['shipping_customer_name'], $data['shipping_address'],
                $data['shipping_address2'], $data['tax_id'], $data['remark'], $data['email'],
                $data['consignee_id'], $data['pay_type'], $data['shop_amount'], $data['logistic_name'],
                $data['from_country_code'], $data['house_number']
            );
            $stmt->execute();
            $orderId = $stmt->insert_id;
            $stmt->close();

            // Insert items
            $itemSql = "INSERT INTO {$this->orderItemTable} (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $itemStmt = $this->conn->prepare($itemSql);

            foreach ($items as $item) {
                $itemStmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
                $itemStmt->execute();
            }

            $itemStmt->close();
            $this->conn->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->conn->rollback();
            return -1;
        }
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
                       u.first_name, u.last_name, u.email AS user_email
                FROM {$this->orderTable} o
                LEFT JOIN {$this->userTable} u ON o.user_id = u.user_id
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


    // Read all orders with user info and nested items
    public function getAll()
    {
        $sql = "SELECT 
                    o.*, 
                    u.first_name, u.last_name, u.email AS user_email,
                    oi.order_item_id, oi.product_id, oi.quantity, oi.price
                FROM {$this->orderTable} o
                LEFT JOIN {$this->userTable} u ON o.user_id = u.user_id
                LEFT JOIN {$this->orderItemTable} oi ON o.order_id = oi.order_id
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
                    'status' => $row['status'],
                    'created_at' => $row['created_at'],
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

        return array_values($orders); // convert from associative to indexed
    }

    // Update order status and remark
    public function update($orderId, $data)
    {
        $sql = "UPDATE {$this->orderTable} SET 
                    status = ?, remark = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $data['status'], $data['remark'], $orderId);
        return $stmt->execute();
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
