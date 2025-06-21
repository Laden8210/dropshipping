<?php

class SupplierProduct
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }



    public function create_product($user_id, $product_name, $product_sku, $category, $description = '', $status = 'active')
    {
        $stmt = $this->conn->prepare("
            INSERT INTO products (user_id, product_name, product_sku, category, description, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssss", $user_id, $product_name, $product_sku, $category, $description, $status);

        if ($stmt->execute()) {
            return [
                'pid' => $this->conn->insert_id,
                'user_id' => $user_id,
                'product_name' => $product_name,
                'product_sku' => $product_sku,
                'category' => $category,
                'status' => $status
            ];
        }
        return false;
    }

    public function get_all_products()
    {
        $stmt = $this->conn->prepare("SELECT * FROM products");
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function get_user_products($user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function get_product_by_id($pid)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE pid = ?");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // ===================== PRICE HISTORY =====================

    public function add_price_history($product_id, $price, $currency = 'USD')
    {
        $stmt = $this->conn->prepare("
            INSERT INTO product_price_history (product_id, price, currency)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("ids", $product_id, $price, $currency);
        return $stmt->execute();
    }

    public function get_price_history($product_id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM product_price_history
            WHERE product_id = ?
            ORDER BY change_date DESC
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        return $history;
    }

    // ===================== PRODUCT IMAGES =====================

    public function add_product_image($product_id, $image_url, $is_primary = false)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO product_images (product_id, image_url, is_primary)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("isi", $product_id, $image_url, $is_primary);
        return $stmt->execute();
    }

    public function get_product_images($product_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
        return $images;
    }

    public function get_primary_image($product_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
