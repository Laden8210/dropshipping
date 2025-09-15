<?php
class StoreProfile
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function create($user_id, $store_name, $description, $logo_url, $address, $phone, $email)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO store_profile (
                user_id, store_name, store_description, store_logo_url, store_address, store_phone, store_email
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $user_id, $store_name, $description, $logo_url, $address, $phone, $email);
        return $stmt->execute();
    }

    public function getAllStores()
    {
        $stmt = $this->conn->prepare("SELECT * FROM store_profile ORDER BY created_at DESC");
        $stmt->execute();
        $result = $stmt->get_result();

        $stores = [];
        while ($row = $result->fetch_assoc()) {
            $stores[] = $row;
        }
        return $stores;
    }

    public function getStoresByUser($user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM store_profile WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $stores = [];
        while ($row = $result->fetch_assoc()) {
            $stores[] = $row;
        }
        return $stores;
    }


    public function getStoreById($store_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM store_profile WHERE store_id = ?");
        $stmt->bind_param("i", $store_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function update($store_id, $store_name, $description, $logo_url, $address, $phone, $email)
    {
        $stmt = $this->conn->prepare("
            UPDATE store_profile 
            SET store_name = ?, store_description = ?, store_logo_url = ?, 
                store_address = ?, store_phone = ?, store_email = ?
            WHERE store_id = ?
        ");
        $stmt->bind_param("ssssssi", $store_name, $description, $logo_url, $address, $phone, $email, $store_id);
        return $stmt->execute();
    }

    public function delete($store_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM store_profile WHERE store_id = ?");
        $stmt->bind_param("i", $store_id);
        return $stmt->execute();
    }

    public function exists($user_id ,$store_id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM store_profile WHERE user_id = ? AND store_id = ?");
        $stmt->bind_param("si", $user_id, $store_id);
        $stmt->execute();
        $count = 0;
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0;
    }
}
