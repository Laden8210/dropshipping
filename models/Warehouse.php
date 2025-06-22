<?php
class Warehouse
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createWarehouse($user_id, $name, $address)
    {

        $check = $this->conn->prepare("SELECT COUNT(*) FROM warehouse WHERE user_id = ?");
        $check->bind_param("s", $user_id);
        $check->execute();
        $count = 0;
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            return false;
        }

        $stmt = $this->conn->prepare("INSERT INTO warehouse (user_id, warehouse_name, warehouse_address) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user_id, $name, $address);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function getWarehousesByUser($user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM warehouse WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $warehouses = [];
        while ($row = $result->fetch_assoc()) {
            $warehouses[] = $row;
        }
        return $warehouses;
    }


    public function getWarehouseById($warehouse_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM warehouse WHERE warehouse_id = ?");
        $stmt->bind_param("i", $warehouse_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function updateWarehouse($warehouse_id, $name, $address)
    {
        $stmt = $this->conn->prepare("UPDATE warehouse SET warehouse_name = ?, warehouse_address = ?, updated_at = NOW() WHERE warehouse_id = ?");
        $stmt->bind_param("ssi", $name, $address, $warehouse_id);
        return $stmt->execute();
    }


    public function deleteWarehouse($warehouse_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM warehouse WHERE warehouse_id = ?");
        $stmt->bind_param("i", $warehouse_id);
        return $stmt->execute();
    }
}
