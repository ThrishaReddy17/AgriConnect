<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $notification_id = intval($_GET['id']);
    
    try {
        $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE id = ?");
        $stmt->execute([$notification_id]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
}
?> 