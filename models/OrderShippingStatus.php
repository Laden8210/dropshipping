<?php

class OrderShippingStatus
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function createShippingStatus($remarks, $trackingNumber, $currentLocation, $latitude, $longitude)
    {
        $query = "INSERT INTO order_shipping_status (remarks, tracking_number, current_location, latitude, longitude) 
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "sssdd",

            $remarks,
            $trackingNumber,
            $currentLocation,
            $latitude,
            $longitude
        );

        return $stmt->execute();
    }

    public function getShippingStatusByOrderId($orderId)
    {
        $query = "SELECT * FROM order_shipping_status WHERE order_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateShippingStatus($shippingStatusId, $remarks, $trackingNumber, $currentLocation, $latitude, $longitude)
    {
        $query = "UPDATE order_shipping_status 
                  SET remarks = ?, tracking_number = ?, current_location = ?, latitude = ?, longitude = ? 
                  WHERE shipping_status_id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ssssdi",
            $remarks,
            $trackingNumber,
            $currentLocation,
            $latitude,
            $longitude,
            $shippingStatusId
        );

        return $stmt->execute();
    }

    public function deleteShippingStatus($shippingStatusId)
    {
        $query = "DELETE FROM order_shipping_status WHERE shipping_status_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $shippingStatusId);
        return $stmt->execute();
    }

    public function getAllShippingStatuses()
    {
        $query = "SELECT 
                o.order_id,
                o.order_number,
                o.tracking_number,
                o.total_amount,
                o.created_at AS order_date,
                
                -- Customer details
                u.user_id AS customer_id,
                u.first_name,
                u.last_name,
                u.email AS customer_email,
                u.phone_number AS customer_phone,
                u.avatar_url AS customer_avatar,
                
                -- Shipping address
                usa.address_line,
                usa.region,
                usa.city,
                usa.brgy,
                usa.postal_code,
                
                -- Store details
                sp.store_id,
                sp.store_name,
                sp.store_logo_url,
                sp.store_phone AS store_contact,
                
                -- Shipping status
                s.shipping_status_id,
                s.remarks,
                s.current_location,
                s.latitude,
                s.longitude,
                s.created_at AS status_update_time
              FROM orders o
              INNER JOIN order_shipping_status s ON o.tracking_number = s.tracking_number
              INNER JOIN users u ON o.user_id = u.user_id
              LEFT JOIN user_shipping_address usa ON o.shipping_address_id = usa.address_id
              INNER JOIN store_profile sp ON o.store_id = sp.store_id
              ORDER BY o.order_id, s.created_at DESC";

        $result = $this->conn->query($query);

        if (!$result) {
            error_log("Shipping status query failed: " . $this->conn->error);
            return false;
        }

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orderId = $row['order_id'];

            // Initialize order structure if not exists
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'order_id' => $orderId,
                    'order_number' => $row['order_number'],
                    'tracking_number' => $row['tracking_number'],
                    'total_amount' => $row['total_amount'],
                    'order_date' => $row['order_date'],
                    'customer' => [
                        'user_id' => $row['customer_id'],
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'],
                        'email' => $row['customer_email'],
                        'phone' => $row['customer_phone'],
                        'avatar_url' => $row['customer_avatar']
                    ],
                    'shipping_address' => [
                        'address_line' => $row['address_line'],
                        'region' => $row['region'],
                        'city' => $row['city'],
                        'barangay' => $row['brgy'],
                        'postal_code' => $row['postal_code']
                    ],
                    'store' => [
                        'store_id' => $row['store_id'],
                        'store_name' => $row['store_name'],
                        'store_logo_url' => $row['store_logo_url'],
                        'store_contact' => $row['store_contact']
                    ],
                    'products' => $this->getOrderProducts($orderId),
                    'shipping_statuses' => []
                ];
            }

            // Add status entry
            $orders[$orderId]['shipping_statuses'][] = [
                'status_id' => $row['shipping_status_id'],
                'remarks' => $row['remarks'],
                'location' => $row['current_location'],
                'coordinates' => [
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude']
                ],
                'update_time' => $row['status_update_time']
            ];
        }

        return array_values($orders);
    }
    
    public function getByTrackingNumber($trackingNumber)
    {
        $query = "SELECT 
                o.order_id,
                o.order_number,
                o.tracking_number,
                o.total_amount,
                o.created_at AS order_date,
                
                u.user_id AS customer_id,
                u.first_name,
                u.last_name,
                u.email AS customer_email,
                u.phone_number AS customer_phone,
                u.avatar_url AS customer_avatar,
                
                usa.address_line,
                usa.region,
                usa.city,
                usa.brgy,
                usa.postal_code,
                
                sp.store_id,
                sp.store_name,
                sp.store_logo_url,
                sp.store_phone AS store_contact,
                
                s.shipping_status_id,
                s.remarks,
                s.current_location,
                s.latitude,
                s.longitude,
                s.created_at AS status_update_time
            FROM orders o
            INNER JOIN order_shipping_status s ON o.tracking_number = s.tracking_number
            INNER JOIN users u ON o.user_id = u.user_id
            LEFT JOIN user_shipping_address usa ON o.shipping_address_id = usa.address_id
            INNER JOIN store_profile sp ON o.store_id = sp.store_id
            WHERE o.tracking_number = ?
            ORDER BY s.created_at DESC";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $trackingNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        $shippingData = [];
        while ($row = $result->fetch_assoc()) {
            if (empty($shippingData)) {
                $shippingData = [
                    'order_id' => $row['order_id'],
                    'order_number' => $row['order_number'],
                    'tracking_number' => $row['tracking_number'],
                    'total_amount' => $row['total_amount'],
                    'order_date' => $row['order_date'],
                    'customer' => [
                        'user_id' => $row['customer_id'],
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'],
                        'email' => $row['customer_email'],
                        'phone' => $row['customer_phone'],
                        'avatar_url' => $row['customer_avatar']
                    ],
                    'shipping_address' => [
                        'address_line' => $row['address_line'],
                        'region' => $row['region'],
                        'city' => $row['city'],
                        'barangay' => $row['brgy'],
                        'postal_code' => $row['postal_code']
                    ],
                    'store' => [
                        'store_id' => $row['store_id'],
                        'store_name' => $row['store_name'],
                        'store_logo_url' => $row['store_logo_url'],
                        'store_contact' => $row['store_contact']
                    ],
                    'products' => $this->getOrderProducts($row['order_id']),
                    'shipping_statuses' => []
                ];
            }

            $shippingData['shipping_statuses'][] = [
                'status_id' => $row['shipping_status_id'],
                'remarks' => $row['remarks'],
                'location' => $row['current_location'],
                'coordinates' => [
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude']
                ],
                'update_time' => $row['status_update_time']
            ];
        }

        return !empty($shippingData) ? $shippingData : false;
    }


    private function getOrderProducts($orderId)
    {
        $products = [];
        $query = "SELECT 
                p.product_id,
                p.product_name,
                p.product_sku,
                oi.quantity,
                oi.price,
                (SELECT image_url FROM product_images 
                 WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) AS primary_image
              FROM order_items oi
              INNER JOIN imported_product ip ON oi.product_id = ip.product_id
              INNER JOIN products p ON ip.product_id = p.product_id
              WHERE oi.order_id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = [
                    'product_id' => $row['product_id'],
                    'name' => $row['product_name'],
                    'sku' => $row['product_sku'],
                    'quantity' => $row['quantity'],
                    'price' => $row['price'],
                    'primary_image' => $row['primary_image']
                ];
            }
        }

        return $products;
    }



    public function getShippingStatusById($shippingStatusId)
    {
        $query = "SELECT * FROM order_shipping_status WHERE shipping_status_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $shippingStatusId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }
}
