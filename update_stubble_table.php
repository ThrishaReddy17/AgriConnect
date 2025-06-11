<?php
include('includes/db.php');

function addColumnIfNotExists($conn, $table, $column, $definition) {
    $stmt = $conn->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
    $stmt->execute([$column]);
    if ($stmt->rowCount() === 0) {
        $conn->exec("ALTER TABLE `$table` ADD COLUMN $column $definition");
        echo "Added column $column to $table<br>";
    }
}

try {
    addColumnIfNotExists($conn, 'stubbleburning', 'collection_date', 'DATE DEFAULT CURRENT_DATE');
    addColumnIfNotExists($conn, 'stubbleburning', 'status', "ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'");
    addColumnIfNotExists($conn, 'stubbleburning', 'created_at', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
    addColumnIfNotExists($conn, 'stubbleburning', 'updated_at', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

    // Update existing records if columns exist
    $columns = $conn->query("SHOW COLUMNS FROM stubbleburning")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (in_array('collection_date', $columns) && in_array('status', $columns)) {
        $update_query = "UPDATE stubbleburning 
                        SET collection_date = IFNULL(collection_date, DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY)),
                            status = IFNULL(status, 'pending')
                        WHERE collection_date IS NULL OR status IS NULL";
        $conn->exec($update_query);
        echo "Updated existing records with default values.<br>";
    }
    echo "Database updated successfully!";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?> 