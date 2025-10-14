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
            p.*,
            pc.category_name,
            ip.profit_margin,
            GROUP_CONCAT(pi.image_url) as image_urls,
            MAX(CASE WHEN pi.is_primary = 1 THEN pi.image_url END) as primary_image_url
        FROM products p
        LEFT JOIN imported_product ip ON p.product_id = ip.product_id
        LEFT JOIN product_categories pc ON p.product_category = pc.category_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        WHERE p.product_id = ? 
        GROUP BY p.product_id, p.product_name, p.description, 
                p.product_category, p.user_id, p.created_at, 
                p.updated_at, pc.category_name, ip.profit_margin
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            // Get variations for this product
            $product['variations'] = $this->getProductSimpleVariations($product_id) ?? [];

            // Parse images if they exist
            if ($product['image_urls']) {
                $product['images'] = explode(',', $product['image_urls']);
                $product['primary_image'] = $product['primary_image_url'] ?: $product['images'][0];
            } else {
                $product['images'] = [];
                $product['primary_image'] = null;
            }
        }

        return $product;
    }

    public function getProductSimpleVariations($productId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                pvs.*,
                i.quantity AS stock_quantity,
                latest_prices.price,
                latest_prices.currency,
                latest_prices.change_date
            FROM product_variations_simple pvs
            RIGHT JOIN inventory i ON pvs.variation_id = i.variation_id
            LEFT JOIN (
                SELECT 
                    variation_id,
                    price,
                    currency,
                    change_date
                FROM product_price_history pph1
                WHERE (pph1.variation_id, pph1.change_date) IN (
                    SELECT 
                        variation_id,
                        MAX(change_date) as latest_date
                    FROM product_price_history
                    GROUP BY variation_id
                )
            ) latest_prices ON pvs.variation_id = latest_prices.variation_id
            WHERE pvs.product_id = ? 
              AND pvs.is_active = 0
            ORDER BY pvs.size, pvs.color
        ");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $variations = [];
        while ($row = $result->fetch_assoc()) {
            $variations[] = $row;
        }
        return $variations;
    }


    public function get_single_product_by_id_by_store($product_id, $store_id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                p.product_id,
                p.user_id,
                p.product_name,
                p.product_sku,    
                p.product_category,
                p.description,
                p.created_at,
                p.status,
                p.updated_at,
                p.is_unlisted,
                ip.profit_margin,
                pc.category_name,
                pc.category_id,
                (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image,
                w.warehouse_name,
                w.warehouse_address,
                sp.store_id,
                sp.store_name,
                sp.store_logo_url,
                sp.store_address
            FROM imported_product ip
            INNER JOIN products p ON ip.product_id = p.product_id
            JOIN product_categories pc ON p.product_category = pc.category_id
            LEFT JOIN warehouse w ON p.user_id = w.user_id
            LEFT JOIN store_profile sp ON ip.store_id = sp.store_id
            WHERE ip.product_id = ? AND ip.store_id = ?
        ");
        $stmt->bind_param("ii", $product_id, $store_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function get_product_variations_with_inventory($product_id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                pvs.variation_id,
                pvs.product_id,
                pvs.size,
                pvs.color,
                pvs.weight,
                pvs.length,
                pvs.width,
                pvs.height,
                pvs.price,
                pvs.currency,
                pvs.sku_suffix,
                pvs.is_active,
                pvs.created_at,
                pvs.updated_at,
                i.inventory_id,
                i.quantity,
                i.created_at as inventory_created_at,
                i.updated_at as inventory_updated_at,
                (
                    SELECT pph.change_date 
                    FROM product_price_history pph 
                    WHERE pph.product_id = pvs.product_id 
                    AND (pph.variation_id = pvs.variation_id OR pph.variation_id IS NULL)
                    ORDER BY pph.change_date DESC 
                    LIMIT 1
                ) as change_date
            FROM product_variations_simple pvs
            LEFT JOIN inventory i ON pvs.variation_id = i.variation_id
            WHERE pvs.product_id = ?
            ORDER BY pvs.variation_id
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $variations = [];
        while ($row = $result->fetch_assoc()) {
            $variations[] = $row;
        }
        return $variations;
    }
    
    public function get_product_images($product_id)
    {
        $stmt = $this->conn->prepare("
            SELECT image_url 
            FROM product_images 
            WHERE product_id = ? 
            ORDER BY is_primary DESC, image_id ASC
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['image_url'];
        }
        return $images;
    }


    public function get_all_products()
    {
        $sql = "
        SELECT 
            p.product_id,
            p.product_name,
            p.product_sku,    
            pc.category_name,
            ip.profit_margin,
            p.status,
            price_data.min_price,
            price_data.max_price,
            price_data.currency,
            ip.store_id,
            -- Get total stock from inventory table (sum of all variation quantities)
            COALESCE((
                SELECT SUM(COALESCE(i.quantity, 0)) 
                FROM product_variations_simple pvs 
                LEFT JOIN inventory i ON pvs.variation_id = i.variation_id
                WHERE pvs.product_id = p.product_id
            ), 0) as total_stock,
            img_data.primary_image,
            w.warehouse_name,
            w.warehouse_address,
            p.created_at
        FROM products p
        JOIN product_categories pc ON p.product_category = pc.category_id
        JOIN imported_product ip ON p.product_id = ip.product_id
        LEFT JOIN warehouse w ON p.user_id = w.user_id
        LEFT JOIN (
            SELECT 
                product_id,
                MIN(price) as min_price,
                MAX(price) as max_price,
                (SELECT currency FROM product_variations_simple pvs2 WHERE pvs2.product_id = pvs1.product_id LIMIT 1) as currency
            FROM product_variations_simple pvs1
            GROUP BY product_id
        ) price_data ON p.product_id = price_data.product_id
        LEFT JOIN (
            SELECT 
                product_id,
                MAX(image_url) as primary_image  -- Use aggregate function
            FROM product_images
            WHERE is_primary = 1
            GROUP BY product_id
        ) img_data ON p.product_id = img_data.product_id
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC, total_stock DESC
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


    public function get_products_by_store($store_id)
    {
        $sql = "
        SELECT 
            p.product_id,
            p.product_name,
            p.product_sku,    
            pc.category_name,
            ip.profit_margin,
            p.status,
            price_data.min_price,
            price_data.max_price,
            price_data.currency,
            ip.store_id,
            -- Get total stock from inventory table (sum of all variation quantities)
            COALESCE((
                SELECT SUM(COALESCE(i.quantity, 0)) 
                FROM product_variations_simple pvs 
                LEFT JOIN inventory i ON pvs.variation_id = i.variation_id
                WHERE pvs.product_id = p.product_id
            ), 0) as total_stock,
            img_data.primary_image,
            w.warehouse_name,
            w.warehouse_address,
            p.created_at
        FROM products p
        JOIN product_categories pc ON p.product_category = pc.category_id
        JOIN imported_product ip ON p.product_id = ip.product_id AND ip.store_id = ?
        LEFT JOIN warehouse w ON p.user_id = w.user_id
        LEFT JOIN (
            SELECT 
                product_id,
                MIN(price) as min_price,
                MAX(price) as max_price,
                (SELECT currency FROM product_variations_simple pvs2 WHERE pvs2.product_id = pvs1.product_id LIMIT 1) as currency
            FROM product_variations_simple pvs1
            GROUP BY product_id
        ) price_data ON p.product_id = price_data.product_id
        LEFT JOIN (
            SELECT 
                product_id,
                MAX(image_url) as primary_image
            FROM product_images
            WHERE is_primary = 1
            GROUP BY product_id
        ) img_data ON p.product_id = img_data.product_id
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC, total_stock DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $store_id); // FIXED: Added parameter binding
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
            p.product_id,
            p.product_name,
            p.product_sku,    
            pc.category_name,
            p.status,
            -- Get price range from variations (include inactive variations too)
            (SELECT MIN(price) FROM product_variations_simple WHERE product_id = p.product_id) as min_price,
            (SELECT MAX(price) FROM product_variations_simple WHERE product_id = p.product_id) as max_price,
            -- Get total stock - sum of all variation stock_quantity + inventory quantity
            COALESCE((
                SELECT SUM(COALESCE(pvs.stock_quantity, 0)) 
                FROM product_variations_simple pvs 
                WHERE pvs.product_id = p.product_id
            ), 0) + 
            COALESCE((
                SELECT SUM(COALESCE(i.quantity, 0)) 
                FROM inventory i 
                WHERE i.product_id = p.product_id
            ), 0) as total_stock,
            -- Get primary image
            (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image,
            w.warehouse_name,
            w.warehouse_address,
            ip.profit_margin,
            p.created_at
        FROM products p
        JOIN product_categories pc ON p.product_category = pc.category_id
        JOIN imported_product ip ON p.product_id = ip.product_id AND ip.user_id = ? AND ip.store_id = ?
        LEFT JOIN warehouse w ON p.user_id = w.user_id
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC, total_stock DESC
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
