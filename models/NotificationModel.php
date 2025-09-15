<?php

class NotificationModel
{

    private $conn;
    private $table = "notifications";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($user_id, $message)
    {
        $sql = "INSERT INTO {$this->table} (user_id, message) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $user_id, $message);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return -1; 
        }
    }

    public function getByUser($user_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        return $notifications;
    }

    public function markAsRead($notification_id, $user_id)
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $notification_id, $user_id);
        return $stmt->execute();
    }

    public function delete($notification_id, $user_id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $notification_id, $user_id);
        return $stmt->execute();
    }

    public function countUnread($user_id)
    {
        $sql = "SELECT COUNT(*) as unread_count FROM {$this->table} WHERE user_id = ? AND is_read = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['unread_count'] ?? 0;
    }
}
