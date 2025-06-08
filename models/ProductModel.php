<?php

class ProductModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function import_product($user_id, $pid, $productName, $supplierId, $productSku, $category)
    {
        $stmt = $this->conn->prepare("INSERT INTO imported_product (user_id, pid, product_name, supplier_id, product_sku, category) VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssss", $user_id, $pid, $productName, $supplierId, $productSku, $category);

        if ($stmt->execute()) {
            return [
                'product_id' => $this->conn->insert_id,
                'user_id' => $user_id,
                'product_name' => $productName,
                'supplier_id' => $supplierId,
                'product_sku' => $productSku,

            ];
        } else {
            return false;
        }
    }

    public function is_product_imported($user_id, $pid)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM imported_product WHERE user_id = ? AND pid = ?");
        $stmt->bind_param("ss", $user_id, $pid);
        $stmt->execute();
        $count = 0;
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0;
    }
    public function get_all_product($user_id){
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

}
