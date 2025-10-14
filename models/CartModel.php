<?php

class CartModel
{
    private $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }


    public function addToCart($userId, $productId, $quantity, $storeId, $variationId)
    {

        $check = $this->db->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $check->bind_param("si", $userId, $productId);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();
            $newQuantity = $row['quantity'] + $quantity;

            $update = $this->db->prepare("UPDATE cart SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE cart_id = ?");
            $update->bind_param("ii", $newQuantity, $row['cart_id']);
            return $update->execute();
        } else {

            $stmt = $this->db->prepare("INSERT INTO cart (user_id, product_id, quantity, store_id, variation_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("siiii", $userId, $productId, $quantity, $storeId, $variationId);
            return $stmt->execute();
        }
    }
    public function getCartItems($userId)
    {
        $stmt = $this->db->prepare("
        SELECT 
            c.cart_id, 
            c.quantity, 
            c.created_at, 
            c.updated_at,
            p.product_name, 
            p.product_sku, 
            p.description,
            p.product_id,
            pi.image_url AS product_image,
            sp.store_id,   
            sp.store_name,
            ip.profit_margin,
            c.variation_id,
            pvs.size,
            pvs.color,
            pvs.weight,
            pvs.length,
            pvs.width,
            pvs.height,
            pvs.price AS base_price,
            pvs.currency,
            ROUND(pvs.price * (1 + ip.profit_margin / 100), 2) AS selling_price
        FROM cart c
        JOIN imported_product ip 
            ON c.product_id = ip.product_id 
            AND c.store_id = ip.store_id
        JOIN products p 
            ON ip.product_id = p.product_id
        JOIN store_profile sp 
            ON c.store_id = sp.store_id
        JOIN product_variations_simple pvs 
            ON c.variation_id = pvs.variation_id
        LEFT JOIN product_images pi 
            ON p.product_id = pi.product_id 
            AND pi.is_primary = 1
        WHERE c.user_id = ?
        ");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $groupedItems = [];
        while ($row = $result->fetch_assoc()) {
            $storeId = $row['store_id'];
    
            if (!isset($groupedItems[$storeId])) {
                $groupedItems[$storeId] = [
                    'store_id' => $storeId,
                    'store_name' => $row['store_name'],
                    'items' => []
                ];
            }
    
            $item = $row;
            unset($item['store_id'], $item['store_name']);
            $groupedItems[$storeId]['items'][] = $item;
        }
    
        return array_values($groupedItems);
    }

    public function updateQuantity($cartId, $quantity)
    {
        $stmt = $this->db->prepare("UPDATE cart SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE cart_id = ?");
        $stmt->bind_param("ii", $quantity, $cartId);
        return $stmt->execute();
    }


    public function removeFromCart($cartId)
    {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE cart_id = ?");
        $stmt->bind_param("i", $cartId);
        return $stmt->execute();
    }


    public function clearCart($userId, $selectProducts)
    {
        $placeholders = implode(',', array_fill(0, count($selectProducts), '?'));
        $types = str_repeat('i', count($selectProducts));

        $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id IN ($placeholders)");
        $stmt->bind_param("s" . $types, $userId, ...$selectProducts);
        return $stmt->execute();
    }

    public function getCartCount($userId)
    {
        $stmt = $this->db->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total_items'] ?? 0;
    }
}
