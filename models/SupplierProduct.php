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
            INSERT INTO products (user_id, product_name, product_sku, product_category, description, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssiss", $user_id, $product_name, $product_sku, $category, $description, $status);

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
        $sql = "
            SELECT 
                products.product_id,
                products.product_name,
                products.product_sku,    
                pc.category_name,
                ph.price, 
                ph.currency,
                ph.change_date,
                pi.image_url AS primary_image
            FROM products
            JOIN product_categories pc ON products.product_category = pc.category_id
            LEFT JOIN (
                SELECT product_id, price, currency, change_date
                FROM product_price_history
                WHERE (product_id, change_date) IN (
                    SELECT product_id, MAX(change_date)
                    FROM product_price_history
                    GROUP BY product_id
                )
            ) ph ON products.product_id = ph.product_id
            LEFT JOIN product_images pi ON products.product_id = pi.product_id AND pi.is_primary = 1
            WHERE products.status = 'active'
            ORDER BY products.created_at DESC
        ";

        $stmt = $this->conn->prepare($sql);
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

    public function get_inventory($user_id)
    {
        $query = "
        SELECT 
            p.product_id,
            p.product_name AS product,
            p.product_sku AS sku,
            pc.category_name AS category,
            IFNULL(i.quantity, 0) AS stock,
            ph.price,
            ph.currency,
            p.status,
            (
                SELECT pi.image_url 
                FROM product_images pi 
                WHERE pi.product_id = p.product_id AND pi.is_primary = TRUE 
                ORDER BY pi.created_at DESC 
                LIMIT 1
            ) AS primary_image
        FROM products p
        LEFT JOIN product_categories pc ON p.product_category = pc.category_id
        LEFT JOIN inventory i ON p.product_id = i.product_id
        LEFT JOIN (
            SELECT ph1.product_id, ph1.price, ph1.currency
            FROM product_price_history ph1
            INNER JOIN (
                SELECT product_id, MAX(change_date) AS max_date
                FROM product_price_history
                GROUP BY product_id
            ) latest ON ph1.product_id = latest.product_id AND ph1.change_date = latest.max_date
        ) ph ON p.product_id = ph.product_id
        WHERE p.user_id = ?
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        return $products;
    }
}
