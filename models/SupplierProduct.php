<?php

class SupplierProduct
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }



    public function create_product($user_id, $product_name, $product_sku, $category, $description = '', $status = 'active', $is_unlisted = false)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO products (user_id, product_name, product_sku, product_category, description, status, is_unlisted)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssissi", $user_id, $product_name, $product_sku, $category, $description, $status, $is_unlisted);

        if ($stmt->execute()) {
            return [
                'pid' => $this->conn->insert_id,
                'user_id' => $user_id,
                'product_name' => $product_name,
                'product_sku' => $product_sku,
                'category' => $category,
                'status' => $status,
                'is_unlisted' => $is_unlisted
            ];
        }
        return false;
    }

    public function update_product($productId, $user_id, $productName, $category, $description = '', $status = 'active')
    {
        $stmt = $this->conn->prepare("
            UPDATE products 
            SET user_id = ?, product_name = ?, product_category = ?, description = ?, status = ?
            WHERE product_id = ?
        ");
        $stmt->bind_param("sssssi", $user_id, $productName, $category, $description, $status, $productId);
        return $stmt->execute();
    }

    public function get_all_products()
    {
        $sql = "
        SELECT 
            products.product_id,
            products.product_name,
            products.product_sku,    
            products.product_weight,
            pc.category_name,
            ph.price, 
            ph.currency,
            ph.change_date,
            pi.image_url AS primary_image,
            w.warehouse_name,
            w.warehouse_address
        FROM products
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
public function get_available_products($user_id, $store_id)
{
    $sql = "
        SELECT 
            p.product_id,
            p.product_name,
            p.product_sku,    
            pc.category_name,
            MAX(ph.price) as price, 
            MAX(ph.currency) as currency,
            MAX(ph.change_date) as change_date,
            MAX(pi.image_url) AS primary_image,
            MAX(w.warehouse_name) as warehouse_name,
            MAX(w.warehouse_address) as warehouse_address,
            p.created_at,
            SUM(i.quantity) AS stock_quantity,
            p.status
        FROM products p
        JOIN product_categories pc ON p.product_category = pc.category_id
        LEFT JOIN product_price_history ph ON p.product_id = ph.product_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN warehouse w ON p.user_id = w.user_id
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.status = 'active'
        AND NOT EXISTS (
            SELECT 1 FROM imported_product ip
            WHERE ip.product_id = p.product_id AND ip.user_id = ? AND ip.store_id = ?
        )
        GROUP BY p.product_id, p.product_name, p.product_sku, pc.category_name, p.created_at, p.status
        ORDER BY p.created_at DESC, stock_quantity DESC
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



    public function add_price_history($product_id, $variation_id, $price, $currency = 'USD')
    {

        // check if price history is price is same and currency is same
        $stmt = $this->conn->prepare("
            SELECT * FROM product_price_history
            WHERE product_id = ? AND variation_id = ? AND price = ? AND currency = ?
        ");
        $stmt->bind_param("iids", $product_id, $variation_id, $price, $currency);
        $stmt->execute();
        $result = $stmt->get_result();
        $price_history = $result->fetch_assoc();
        if ($price_history) {
            return true;
        }

        $stmt = $this->conn->prepare("
            INSERT INTO product_price_history (product_id, variation_id, price, currency)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iids", $product_id, $variation_id, $price, $currency);
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
            pvs.variation_id,
            pvs.size,
            pvs.color,
            pvs.weight,
            pvs.length,
            pvs.width,
            pvs.height,
            COALESCE(pvs.price, ph.price) AS price,
            COALESCE(pvs.currency, ph.currency, 'USD') AS currency,
            p.status,
            p.is_unlisted,
            COALESCE(vi.quantity, pvs.stock_quantity, 0) AS stock,
            pvs.is_active as variation_active,
            (
                SELECT pi.image_url 
                FROM product_images pi 
                WHERE pi.product_id = p.product_id AND pi.is_primary = TRUE 
                ORDER BY pi.created_at DESC 
                LIMIT 1
            ) AS primary_image
        FROM products p
        LEFT JOIN product_categories pc ON p.product_category = pc.category_id
        LEFT JOIN product_variations_simple pvs ON p.product_id = pvs.product_id  -- No is_active filter
        LEFT JOIN inventory vi ON pvs.variation_id = vi.variation_id
        LEFT JOIN (
            SELECT 
                product_id,
                price,
                currency
            FROM (
                SELECT 
                    product_id,
                    price,
                    currency,
                    ROW_NUMBER() OVER (PARTITION BY product_id ORDER BY change_date DESC, history_id DESC) as rn
                FROM product_price_history
            ) ranked
            WHERE rn = 1
        ) ph ON p.product_id = ph.product_id
        WHERE p.user_id = ?
        ORDER BY p.product_id, pvs.variation_id
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

    public function get_products($user_id)
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
            p.is_unlisted,
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
            SELECT 
                product_id,
                price,
                currency
            FROM (
                SELECT 
                    product_id,
                    price,
                    currency,
                    ROW_NUMBER() OVER (PARTITION BY product_id ORDER BY change_date DESC, history_id DESC) as rn
                FROM product_price_history
            ) ranked
            WHERE rn = 1
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

    // Simplified Variation Methods (Size, Color, Weight, Price, Dimensions)

    /**
     * Create a simple product variation (size, color, weight, price, dimensions)
     */
    public function createSimpleVariation($productId, $size = null, $color = null, $weight = null, $length = null, $width = null, $height = null, $price, $currency = 'USD', $skuSuffix = null, $stockQuantity = 0)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO product_variations_simple (product_id, size, color, weight, length, width, height, price, currency, sku_suffix, stock_quantity)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssddddssi", $productId, $size, $color, $weight, $length, $width, $height, $price, $currency, $skuSuffix, $stockQuantity);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function updateSimpleVariation($productId, $variationId, $size = null, $color = null, $weight = null, $length = null, $width = null, $height = null, $price, $currency = 'USD', $skuSuffix = null, $stockQuantity = 0)
    {
        // If variation_id is empty or 0, we need to INSERT instead of UPDATE
        if (empty($variationId) || $variationId == 0) {
            return $this->createSimpleVariation($productId, $size, $color, $weight, $length, $width, $height, $price, $currency, $skuSuffix, $stockQuantity);
        }

        $stmt = $this->conn->prepare("
            UPDATE product_variations_simple 
            SET size = ?, color = ?, weight = ?, length = ?, width = ?, height = ?, price = ?, currency = ?, sku_suffix = ?, stock_quantity = ?, is_active = 0
            WHERE variation_id = ?
        ");

        // Corrected parameter types and order:
        // 11 parameters: size(s), color(s), weight(d), length(d), width(d), height(d), price(d), currency(s), sku_suffix(s), stock_quantity(i), variation_id(i)
        $stmt->bind_param(
            "ssdddddsdii",
            $size,           // s - string
            $color,          // s - string  
            $weight,         // d - double
            $length,         // d - double
            $width,          // d - double
            $height,         // d - double
            $price,          // d - double (assuming price is decimal)
            $currency,       // s - string
            $skuSuffix,      // s - string
            $stockQuantity,  // i - integer
            $variationId     // i - integer
        );

        return $stmt->execute();
    }



    /**
     * Get all simple variations for a product
     */
    public function getProductSimpleVariations($productId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                pvs.*,
                latest_prices.price,
                latest_prices.currency,
                latest_prices.change_date
            FROM product_variations_simple pvs
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
    /**
     * Update simple variation price
     */
    public function updateSimpleVariationPrice($variationId, $price, $currency = 'USD')
    {
        $stmt = $this->conn->prepare("
            UPDATE product_variations_simple 
            SET price = ?, currency = ? 
            WHERE variation_id = ?
        ");
        $stmt->bind_param("dsi", $price, $currency, $variationId);
        return $stmt->execute();
    }

    /**
     * Update simple variation stock
     */
    public function updateSimpleVariationStock($variationId, $quantity)
    {
        $stmt = $this->conn->prepare("
            UPDATE product_variations_simple 
            SET stock_quantity = ? 
            WHERE variation_id = ?
        ");
        $stmt->bind_param("ii", $quantity, $variationId);
        return $stmt->execute();
    }

    /**
     * Get simple variation by ID
     */
    public function getSimpleVariationById($variationId)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM product_variations_simple 
            WHERE variation_id = ? AND is_active = 0
        ");
        $stmt->bind_param("i", $variationId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Delete simple variation
     */
    public function deleteSimpleVariation($variationId)
    {
        $stmt = $this->conn->prepare("
            UPDATE product_variations_simple 
            SET is_active = 1 
            WHERE variation_id = ?
        ");
        $stmt->bind_param("i", $variationId);
        return $stmt->execute();
    }

    /**
     * Delete all variations for a product
     */
    public function deleteAllVariationsForProduct($productId)
    {
        $stmt = $this->conn->prepare("
            UPDATE product_variations_simple 
            SET is_active = 1 
            WHERE product_id = ?
        ");
        $stmt->bind_param("i", $productId);
        return $stmt->execute();
    }

    /**
     * Get single product with variations by supplier
     */
    public function get_single_product_by_supplier($productId, $userId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                p.*,
                pc.category_name,
                GROUP_CONCAT(pi.image_url) as image_urls,
                MAX(CASE WHEN pi.is_primary = 1 THEN pi.image_url END) as primary_image_url
            FROM products p
            LEFT JOIN product_categories pc ON p.product_category = pc.category_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            WHERE p.product_id = ? AND p.user_id = ?
            GROUP BY p.product_id, p.product_name, p.description, 
                    p.product_category, p.user_id, p.created_at, 
                     p.updated_at, pc.category_name
        ");
        $stmt->bind_param("is", $productId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            // Get variations for this product
            $product['variations'] = $this->getProductSimpleVariations($productId) ?? [];

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

    /**
     * Update product unlisted status
     */
    public function updateProductUnlistedStatus($productId, $isUnlisted)
    {
        $stmt = $this->conn->prepare("
            UPDATE products 
            SET is_unlisted = ?, status = CASE WHEN ? = 1 THEN 'unlisted' ELSE status END
            WHERE product_id = ?
        ");
        $stmt->bind_param("iii", $isUnlisted, $isUnlisted, $productId);
        return $stmt->execute();
    }

    /**
     * Get available products (excluding unlisted)
     */
    public function getAvailableProductsForImport($user_id, $store_id)
    {
        $sql = "
            SELECT 
                products.product_id,
                products.product_name,
                products.product_sku,    
                products.product_weight,
                products.length,
                products.width,
                products.height,
                pc.category_name,
                ph.price, 
                ph.currency,
                ph.change_date,
                pi.image_url AS primary_image,
                w.warehouse_name,
                w.warehouse_address
            FROM products
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
            WHERE products.status = 'active' 
            AND products.is_unlisted = FALSE
            AND NOT EXISTS (
                SELECT 1 FROM imported_product ip
                WHERE ip.product_id = products.product_id AND ip.user_id = ? AND ip.store_id = ?
            )
            ORDER BY products.created_at DESC
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

    /**
     * Delete variation
     */
    public function deleteVariation($variationId)
    {
        $stmt = $this->conn->prepare("
            UPDATE product_variations 
            SET is_active = 1 
            WHERE variation_id = ?
        ");
        $stmt->bind_param("i", $variationId);
        return $stmt->execute();
    }

    /**
     * Get variation by ID
     */
    public function getVariationById($variationId)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM product_variations 
            WHERE variation_id = ? AND is_active = 0
        ");
        $stmt->bind_param("i", $variationId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
