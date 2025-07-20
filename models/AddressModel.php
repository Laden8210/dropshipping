<?php


class AddressModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function saveAddress($user_id, $address_data) {
        $stmt = $this->db->prepare("INSERT INTO user_shipping_address (user_id, address_line, region, city, brgy, postal_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $user_id, $address_data['address_line'], $address_data['region'], $address_data['city'], $address_data['brgy'], $address_data['postal_code']);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getAddress($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM user_shipping_address WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
    public function updateAddress($user_id, $address_data) {
        $stmt = $this->db->prepare("UPDATE user_shipping_address SET address_line = ?, region = ?, city = ?, brgy = ?, postal_code = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $address_data['address_line'], $address_data['region'], $address_data['city'], $address_data['brgy'], $address_data['postal_code'], $user_id);
        
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Address updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update address: ' . $stmt->error];
        }
    }
    public function deleteAddress($user_id) {
        $stmt = $this->db->prepare("DELETE FROM user_shipping_address WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Address deleted successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete address: ' . $stmt->error];
        }
    }
    public function getAllAddresses($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM user_shipping_address WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
    

}