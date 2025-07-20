<?php 

class OrderStatusHistory
{
    private $conn;
    private $table = "order_status_history";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($orderId, $status)
    {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (order_id, status) VALUES (?, ?)");
        $stmt->bind_param("is", $orderId, $status);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get status history for a specific order
    public function getByOrderId($orderId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE order_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $history = [];

        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }

        return $history;
    }

}