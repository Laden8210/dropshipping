# Database Schema Documentation

## 📡 Database Connection

Ensure your database connection is configured as follows:

```php
<?php
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_database_user';
$password = 'your_database_password';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected successfully.";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
