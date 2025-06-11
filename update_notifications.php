<?php
include 'includes/db.php';

try {
    // Add user_id column to notifications table
    $alter_query = "ALTER TABLE notifications 
                   ADD COLUMN IF NOT EXISTS user_id INT,
                   ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";
    
    $conn->exec($alter_query);
    
    // Update existing notifications to link them to the correct user
    // For order notifications, get the user_id from the orders table
    $update_query = "UPDATE notifications n 
                    JOIN orders o ON n.order_id = o.id 
                    SET n.user_id = o.user_id 
                    WHERE n.order_id IS NOT NULL AND n.user_id IS NULL";
    $conn->exec($update_query);
    
    echo "✅ Database updated successfully!<br>";
    echo "Added column:<br>";
    echo "- notifications: user_id (INT, foreign key to users table)<br>";
    
    // Verify the table structure
    echo "<br>Verifying notifications table structure...<br>";
    $stmt = $conn->query("DESCRIBE notifications");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Notifications table columns:<br>";
    echo implode(', ', $columns);

} catch(PDOException $e) {
    echo "❌ Error updating database: " . $e->getMessage() . "<br>";
}
?> 