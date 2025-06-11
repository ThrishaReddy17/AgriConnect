<?php
include 'includes/db.php';

try {
    // Test database connection
    echo "Testing database connection...<br>";
    if($conn) {
        echo "✅ Database connected successfully<br><br>";
        
        // Check if users table exists
        $stmt = $conn->query("SHOW TABLES LIKE 'users'");
        if($stmt->rowCount() > 0) {
            echo "✅ Users table exists<br>";
            
            // Check users table structure
            $stmt = $conn->query("DESCRIBE users");
            echo "<br>Users table structure:<br>";
            echo "<pre>";
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                print_r($row);
            }
            echo "</pre>";
            
            // Check if any admin users exist
            $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'admin'");
            $stmt->execute();
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<br>Existing admin users:<br>";
            if(count($admins) > 0) {
                echo "<pre>";
                print_r($admins);
                echo "</pre>";
            } else {
                echo "No admin users found<br>";
            }
        } else {
            echo "❌ Users table does not exist<br>";
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>