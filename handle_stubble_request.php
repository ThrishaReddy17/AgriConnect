<?php
session_start();
include('../includes/db.php');

// Check if user is logged in and is an organization
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'organization') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stubble_id = isset($_POST['stubble_id']) ? intval($_POST['stubble_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($stubble_id <= 0 || !in_array($action, ['accept', 'reject'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request parameters']);
        exit();
    }
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Get stubble request details
        $stmt = $conn->prepare("SELECT farmer_name, stubble_quantity, location, status FROM stubbleburning WHERE id = ?");
        $stmt->execute([$stubble_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$request) {
            throw new Exception('Stubble request not found');
        }
        
        if ($request['status'] !== 'pending') {
            throw new Exception('This request has already been processed');
        }
        
        // Update stubble request status
        $new_status = $action === 'accept' ? 'accepted' : 'rejected';
        $stmt = $conn->prepare("UPDATE stubbleburning SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$new_status, $stubble_id]);
        
        // Create notification for farmer
        $message = $action === 'accept' 
            ? "Your stubble burning request for {$request['stubble_quantity']} tons at {$request['location']} has been accepted."
            : "Your stubble burning request for {$request['stubble_quantity']} tons at {$request['location']} has been rejected.";
        
        $stmt = $conn->prepare("INSERT INTO notifications (message, status, stubble_id, created_at) VALUES (?, 'unread', ?, CURRENT_TIMESTAMP)");
        $stmt->execute([$message, $stubble_id]);
        
        // Mark any existing notifications for this stubble request as read
        $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE stubble_id = ? AND status = 'unread'");
        $stmt->execute([$stubble_id]);
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => $action === 'accept' ? 'Request accepted successfully' : 'Request rejected successfully'
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        error_log("Error in handle_stubble_request.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error processing request: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 