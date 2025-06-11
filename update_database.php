<?php
include 'includes/db.php';

try {
    // Add missing columns to product_requests table
    $alter_queries = [
        "ALTER TABLE product_requests 
         ADD COLUMN IF NOT EXISTS farmer_id INT,
         ADD COLUMN IF NOT EXISTS description TEXT,
         ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         ADD COLUMN IF NOT EXISTS status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'",
        
        // Add missing columns to notifications table
        "ALTER TABLE notifications 
         ADD COLUMN IF NOT EXISTS farmer_id INT,
         ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         ADD COLUMN IF NOT EXISTS status ENUM('unread', 'read') DEFAULT 'unread'"
    ];

    // Execute each ALTER query
    foreach ($alter_queries as $query) {
        $conn->exec($query);
    }

    echo "✅ Database updated successfully!<br>";
    echo "Added columns:<br>";
    echo "- product_requests: farmer_id, description, created_at, status<br>";
    echo "- notifications: farmer_id, created_at, status<br>";
    
    // Verify the tables structure
    echo "<br>Verifying table structure...<br>";
    
    $tables = ['product_requests', 'notifications'];
    foreach ($tables as $table) {
        $stmt = $conn->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<br>Table '$table' columns:<br>";
        echo implode(', ', $columns);
    }

} catch(PDOException $e) {
    echo "❌ Error updating database: " . $e->getMessage() . "<br>";
}
?> 