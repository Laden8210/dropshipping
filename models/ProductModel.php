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
    public function get_all_product($user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM imported_product WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function get_all_products()
    {
        $stmt = $this->conn->prepare("SELECT * FROM imported_product");
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
}
