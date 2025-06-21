<?php

class Category
{
    private $conn;
    private $table = "product_categories";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create a new category
    public function create($category_name, $user_id)
    {
        $sql = "INSERT INTO {$this->table} (category_name, user_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $category_name, $user_id);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return -1; 
        }
    }

    // Get all categories
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get categories by user
    public function getByUser($user_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND is_deleted = 0 ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }

    public function delete($category_id, $user_id)
    {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE category_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $category_id, $user_id);
        return $stmt->execute();
    }


    public function exists($category_name, $user_id)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE category_name = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $category_name, $user_id);
        $stmt->execute();
        $count = 0;
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0;
    }

    public function existsById($category_id, $user_id)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE category_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $category_id, $user_id);
        $stmt->execute();
        $count = 0;
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0;
    }
}
