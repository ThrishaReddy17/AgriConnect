<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // Fetch product request details
        $stmt = $conn->prepare("SELECT farmer_id, farmer_name, product_name, phone_number FROM product_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            // Create notification for the farmer
            $message = "Your request to add '{$request['product_name']}' has been rejected. Please contact admin for more details.";
            $stmt = $conn->prepare("INSERT INTO notifications (farmer_id, message, status) VALUES (?, ?, 'unread')");
            $stmt->execute([$request['farmer_id'], $message]);
            $farmer_notification_id = $conn->lastInsertId();

            // Update the request status
            $stmt = $conn->prepare("UPDATE product_requests SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$id]);

            // Create admin notification
            $admin_message = "Product request for '{$request['product_name']}' from {$request['farmer_name']} has been rejected.";
            $stmt = $conn->prepare("INSERT INTO notifications (message, status) VALUES (?, 'unread')");
            $stmt->execute([$admin_message]);
            $admin_notification_id = $conn->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => $admin_message,
                'notification_id' => $admin_notification_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Request not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
}
?> 