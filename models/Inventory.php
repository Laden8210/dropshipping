<?php

class Inventory
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getCurrentStock($product_id)
    {
        $stmt = $this->db->prepare("SELECT quantity FROM inventory WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['quantity'] : 0;
    }

    public function getStockMovementSummary($product_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN movement_type = 'in' THEN quantity ELSE 0 END) AS total_in,
                SUM(CASE WHEN movement_type = 'out' THEN quantity ELSE 0 END) AS total_out
            FROM stock_movements
            WHERE product_id = ?
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateQuantity($product_id, $quantityChange)
    {

        $stmt = $this->db->prepare("SELECT quantity FROM inventory WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $inventory = $result->fetch_assoc();

        if (!$inventory) {
            return false;
        }

        $current_quantity = (int)$inventory['quantity'];
        $new_quantity = $current_quantity + $quantityChange;

        if ($new_quantity < 0) {
            return false;
        }

        $updateStmt = $this->db->prepare("UPDATE inventory SET quantity = ?, updated_at = NOW() WHERE product_id = ?");
        $updateStmt->bind_param("ii", $new_quantity, $product_id);
        return $updateStmt->execute();
    }

    public function getCurrentStockVariant($product_id, $variation_id)
    {
        $stmt = $this->db->prepare("SELECT quantity FROM inventory WHERE product_id = ? AND variation_id = ?");
        $stmt->bind_param("ii", $product_id, $variation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['quantity'] : 0;
    }

    public function addStockMovement($product_id, $variation_id, $quantity, $movement_type, $reason = null)
    {
        $this->db->begin_transaction();

        try {

            $stmt = $this->db->prepare("SELECT inventory_id, quantity FROM inventory WHERE product_id = ? AND variation_id = ?");
            $stmt->bind_param("ii", $product_id, $variation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $inventory = $result->fetch_assoc();

            if (!$inventory) {

                $new_quantity = ($movement_type === 'in') ? $quantity : 0;

                $stmt = $this->db->prepare("INSERT INTO inventory (product_id, variation_id, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $product_id, $variation_id, $new_quantity);
                $stmt->execute();

                $inventory_id = $this->db->insert_id;
                $current_quantity = $new_quantity;
            } else {
                $inventory_id = $inventory['inventory_id'];
                $current_quantity = (int)$inventory['quantity'];

                if ($movement_type === 'in') {
                    $new_quantity = $current_quantity + $quantity;
                } elseif ($movement_type === 'out') {
                    if ($current_quantity < $quantity) {
                        throw new Exception("Not enough stock to remove.");
                    }
                    $new_quantity = $current_quantity - $quantity;
                } else {
                    return false;
                }

                $stmt = $this->db->prepare("UPDATE inventory SET quantity = ?, updated_at = NOW() WHERE inventory_id = ?");
                $stmt->bind_param("ii", $new_quantity, $inventory_id);
                $stmt->execute();
            }


            $movement_number = $this->generateMovementNumber();

            $stmt = $this->db->prepare("
            INSERT INTO stock_movements (
                movement_number, product_id, inventory_id, quantity, movement_type, reason, variation_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
            $stmt->bind_param("siiissi", $movement_number, $product_id, $inventory_id, $quantity, $movement_type, $reason, $variation_id);
            $stmt->execute();

            $this->db->commit();

            return true;
        } catch (Exception $e) {
            $this->db->rollback();

            return false;
        }
    }

    public function getStockMovements($product_id)
    {
        $stmt = $this->db->prepare("
        SELECT 
            sm.*,
            p.product_name,
            i.quantity AS current_stock,
            (
                SELECT price 
                FROM product_price_history pph 
                WHERE pph.product_id = sm.product_id 
                AND pph.change_date <= sm.created_at 
                ORDER BY pph.change_date DESC 
                LIMIT 1
            ) AS price,
            (
                SELECT currency 
                FROM product_price_history pph 
                WHERE pph.product_id = sm.product_id 
                AND pph.change_date <= sm.created_at 
                ORDER BY pph.change_date DESC 
                LIMIT 1
            ) AS currency,
            (
                SELECT change_date 
                FROM product_price_history pph 
                WHERE pph.product_id = sm.product_id 
                AND pph.change_date <= sm.created_at 
                ORDER BY pph.change_date DESC 
                LIMIT 1
            ) AS price_date
        FROM stock_movements sm
        JOIN products p ON sm.product_id = p.product_id
        JOIN inventory i ON sm.inventory_id = i.inventory_id
        WHERE sm.product_id = ?
        ORDER BY sm.created_at DESC
    ");

        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $movements = [];
        while ($row = $result->fetch_assoc()) {
            $movements[] = $row;
        }
        return $movements;
    }



    public function getStockMovementsVariant($product_id, $variation_id)
    {
        // Get all stock movements
        $stmt = $this->db->prepare("
        SELECT 
            sm.*,
            p.product_name,
            i.quantity AS current_stock,
            (
                SELECT price 
                FROM product_price_history pph 
                WHERE pph.product_id = sm.product_id 
                AND pph.change_date <= sm.created_at 
                ORDER BY pph.change_date DESC 
                LIMIT 1
            ) AS price,
            (
                SELECT currency 
                FROM product_price_history pph 
                WHERE pph.product_id = sm.product_id 
                AND pph.change_date <= sm.created_at 
                ORDER BY pph.change_date DESC 
                LIMIT 1
            ) AS currency,
            (
                SELECT change_date 
                FROM product_price_history pph 
                WHERE pph.product_id = sm.product_id 
                AND pph.change_date <= sm.created_at 
                ORDER BY pph.change_date DESC 
                LIMIT 1
            ) AS price_date
        FROM stock_movements sm
        JOIN products p ON sm.product_id = p.product_id
        JOIN inventory i ON sm.inventory_id = i.inventory_id
        WHERE sm.product_id = ? AND sm.variation_id = ?
        ORDER BY sm.created_at DESC
    ");

        $stmt->bind_param("ii", $product_id, $variation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $movements = [];
        while ($row = $result->fetch_assoc()) {
            $movements[] = $row;
        }

        // Calculate summary metrics
        $summary = $this->calculateStockSummary($movements);

        return [
            'movements' => $movements,
            'summary' => $summary
        ];
    }

    private function calculateStockSummary($movements)
    {
        $summary = [
            'current_stock' => 0,
            'total_incoming' => 0,
            'total_sales' => 0,
            'total_adjustments' => 0,
            'total_outgoing' => 0
        ];

        if (empty($movements)) {
            return $summary;
        }

        // Get current stock from the first movement (latest record)
        $summary['current_stock'] = $movements[0]['current_stock'] ?? 0;

        foreach ($movements as $movement) {
            $quantity = abs($movement['quantity'] ?? 0);
            $movement_type = $movement['movement_type'] ?? '';
            $reason = $movement['reason'] ?? '';

            // Categorize based on movement type and reason
            if ($movement_type === 'in') {
                // All incoming movements count as total_incoming
                $summary['total_incoming'] += $quantity;
            } elseif ($movement_type === 'out') {
                // Check if it's a sale (contains "Order ID" in reason) or adjustment
                if (strpos($reason, 'Order ID:') !== false) {
                    // Sales - orders
                    $summary['total_sales'] += $quantity;
                } else {
                    // Adjustments - all other out movements
                    $summary['total_adjustments'] += $quantity;
                }
            }
        }

        // Calculate total outgoing
        $summary['total_outgoing'] = $summary['total_sales'] + $summary['total_adjustments'];

        return $summary;
    }

    private function generateMovementNumber()
    {
        return 'SM-' . date('mdY-His');
    }
}
