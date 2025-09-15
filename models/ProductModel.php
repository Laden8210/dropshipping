<?php

class ProductModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function import_product($user_id, $product_id, $store_id)
    {
        $stmt = $this->conn->prepare("
        INSERT INTO imported_product (user_id, product_id, store_id)
        VALUES (?, ?, ?)
    ");

        $stmt->bind_param("sii", $user_id, $product_id, $store_id);

        if ($stmt->execute()) {
            return [
                'imported_product_id' => $this->conn->insert_id,
                'user_id' => $user_id,
                'product_id' => $product_id,
                'store_id' => $store_id
            ];
        } else {
            return [
                'status' => 'error',
                'message' => $stmt->error
            ];
        }
    }


    public function is_product_imported($user_id, $pid)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM imported_product WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ss", $user_id, $pid);
        $stmt->execute();
        $count = 0;
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0;
    }

    public function get_single_product_by_id($product_id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                products.product_id,
                products.product_name,
                products.product_sku,    
                products.product_weight,
                products.status,
                products.description,
                ip.profit_margin,
                pc.category_name,
                pc.category_id,
                ph.price, 
                ph.currency,
                ph.change_date,
                pi.image_url AS primary_image,
                w.warehouse_name,
                w.warehouse_address,
                i.quantity AS current_stock
            FROM imported_product ip
            INNER JOIN products ON ip.product_id = products.product_id
            JOIN product_categories pc ON products.product_category = pc.category_id
            LEFT JOIN (
                SELECT p1.*
                FROM product_price_history p1
                INNER JOIN (
                    SELECT product_id, MAX(change_date) AS max_date
                    FROM product_price_history
                    GROUP BY product_id
                ) p2 ON p1.product_id = p2.product_id AND p1.change_date = p2.max_date
            ) ph ON products.product_id = ph.product_id
            LEFT JOIN product_images pi ON products.product_id = pi.product_id AND pi.is_primary = 1
            LEFT JOIN warehouse w ON products.user_id = w.user_id
            LEFT JOIN inventory i ON products.product_id = i.product_id
            WHERE  ip.product_id = ?
        ");
        $stmt->bind_param("i",  $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function get_single_product_by_id_by_store($product_id, $store_id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                products.product_id,
                products.product_name,
                products.product_sku,    
                products.product_weight,
                products.status,
                products.description,
                ip.profit_margin,
                pc.category_name,
                pc.category_id,
                ph.price, 
                ph.currency,
                ph.change_date,
                pi.image_url AS primary_image,
                w.warehouse_name,
                w.warehouse_address,
                i.quantity AS current_stock,
                sp.store_id,
                sp.store_name,
                sp.store_logo_url,
                sp.store_address,
                sp.store_logo_url
            FROM imported_product ip
            INNER JOIN products ON ip.product_id = products.product_id
            JOIN product_categories pc ON products.product_category = pc.category_id
            LEFT JOIN (
                SELECT p1.*
                FROM product_price_history p1
                INNER JOIN (
                    SELECT product_id, MAX(change_date) AS max_date
                    FROM product_price_history
                    GROUP BY product_id
                ) p2 ON p1.product_id = p2.product_id AND p1.change_date = p2.max_date
            ) ph ON products.product_id = ph.product_id
            LEFT JOIN product_images pi ON products.product_id = pi.product_id AND pi.is_primary = 1
            LEFT JOIN warehouse w ON products.user_id = w.user_id
            LEFT JOIN inventory i ON products.product_id = i.product_id
            LEFT JOIN store_profile sp ON ip.store_id = sp.store_id
            WHERE ip.product_id = ? AND ip.store_id = ?
        ");
        $stmt->bind_param("ii",  $product_id, $store_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function get_all_products()
    {
        $stmt = $this->conn->prepare("
               SELECT 
            products.product_id,
            products.product_name,
            products.product_sku,    
            products.product_weight,
            products.status,
            ip.profit_margin,
            ip.store_id,
            pc.category_name,
            ph.price, 
            ph.currency,
            ph.change_date,
            pi.image_url AS primary_image,
            w.warehouse_name,
            w.warehouse_address,
            i.quantity AS current_stock
        FROM imported_product ip
        INNER JOIN products ON ip.product_id = products.product_id
        JOIN product_categories pc ON products.product_category = pc.category_id
        LEFT JOIN (
            SELECT p1.*
            FROM product_price_history p1
            INNER JOIN (
                SELECT product_id, MAX(change_date) AS max_date
                FROM product_price_history
                GROUP BY product_id
            ) p2 ON p1.product_id = p2.product_id AND p1.change_date = p2.max_date
        ) ph ON products.product_id = ph.product_id
        LEFT JOIN product_images pi ON products.product_id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN warehouse w ON products.user_id = w.user_id
        LEFT JOIN inventory i ON products.product_id = i.product_id
        ORDER BY ip.created_at DESC
        
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }


       public function get_products_by_store($store_id)
    {
        $stmt = $this->conn->prepare("
               SELECT 
            products.product_id,
            products.product_name,
            products.product_sku,    
            products.product_weight,
            products.status,
            ip.profit_margin,
            ip.store_id,
            pc.category_name,
            ph.price, 
            ph.currency,
            ph.change_date,
            pi.image_url AS primary_image,
            w.warehouse_name,
            w.warehouse_address,
            i.quantity AS current_stock
        FROM imported_product ip
        INNER JOIN products ON ip.product_id = products.product_id
        JOIN product_categories pc ON products.product_category = pc.category_id
        LEFT JOIN (
            SELECT p1.*
            FROM product_price_history p1
            INNER JOIN (
                SELECT product_id, MAX(change_date) AS max_date
                FROM product_price_history
                GROUP BY product_id
            ) p2 ON p1.product_id = p2.product_id AND p1.change_date = p2.max_date
        ) ph ON products.product_id = ph.product_id
        LEFT JOIN product_images pi ON products.product_id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN warehouse w ON products.user_id = w.user_id
        LEFT JOIN inventory i ON products.product_id = i.product_id
        WHERE ip.store_id = ?
        ORDER BY ip.created_at DESC
        ");
        $stmt->bind_param("i", $store_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }


    public function get_imported_products($user_id, $store_id)
    {
        $sql = "
        SELECT 
            products.product_id,
            products.product_name,
            products.product_sku,    
            products.product_weight,
            products.status,
            ip.profit_margin,
            pc.category_name,
            ph.price, 
            ph.currency,
            ph.change_date,
            pi.image_url AS primary_image,
            w.warehouse_name,
            w.warehouse_address,
            i.quantity AS current_stock
        FROM imported_product ip
        INNER JOIN products ON ip.product_id = products.product_id
        JOIN product_categories pc ON products.product_category = pc.category_id
        LEFT JOIN (
            SELECT p1.*
            FROM product_price_history p1
            INNER JOIN (
                SELECT product_id, MAX(change_date) AS max_date
                FROM product_price_history
                GROUP BY product_id
            ) p2 ON p1.product_id = p2.product_id AND p1.change_date = p2.max_date
        ) ph ON products.product_id = ph.product_id
        LEFT JOIN product_images pi ON products.product_id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN warehouse w ON products.user_id = w.user_id
        LEFT JOIN inventory i ON products.product_id = i.product_id
        WHERE ip.user_id = ? AND ip.store_id = ?
        ORDER BY ip.created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $user_id, $store_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }



    public function update_profit_margin($user_id, $product_id, $new_profit_margin)
    {
        $stmt = $this->conn->prepare("UPDATE imported_product SET profit_margin = ? WHERE product_id = ? AND user_id = ?");
        $stmt->bind_param("dss", $new_profit_margin, $product_id, $user_id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function get_price_product_history($product_id)
    {
        $stmt = $this->conn->prepare("
        SELECT price, currency, change_date 
        FROM product_price_history 
        WHERE product_id = ? 
        ORDER BY change_date DESC
    ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $price_history = [];
        while ($row = $result->fetch_assoc()) {
            $price_history[] = $row;
        }
        return $price_history;
    }
}
