<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../includes/db.php';

// First, check if the farmer_id column exists
$check_column = $conn->query("SHOW COLUMNS FROM product_requests LIKE 'farmer_id'");
$column_exists = $check_column->rowCount() > 0;

// Fetch notifications with order information
$stmt = $conn->prepare("
    SELECT n.id, n.message, n.status, n.created_at, n.order_id, 
           o.name as customer_name, o.total_cost, o.payment_method
    FROM notifications n
    LEFT JOIN orders o ON n.order_id = o.id
    WHERE n.status = 'unread'
    ORDER BY n.created_at DESC
    LIMIT 5
");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending product requests with appropriate columns
if ($column_exists) {
    $stmt = $conn->prepare("SELECT id, farmer_id, farmer_name, phone_number, product_name, price_per_kg, quantity, description, status, created_at FROM product_requests WHERE status = 'pending' ORDER BY created_at DESC");
} else {
    // Fallback query without farmer_id
    $stmt = $conn->prepare("SELECT id, farmer_name, phone_number, product_name, price_per_kg, quantity, status FROM product_requests WHERE status = 'pending' ORDER BY id DESC");
}
$stmt->execute();
$product_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle notification read status
if (isset($_GET['notification_id']) && is_numeric($_GET['notification_id'])) {
    $notification_id = intval($_GET['notification_id']);
    $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE id = ?");
    $stmt->execute([$notification_id]);
    header("Location: dashboard.php");
    exit();
}

// If farmer_id column doesn't exist, try to add it
if (!$column_exists) {
    try {
        $conn->exec("ALTER TABLE product_requests 
                    ADD COLUMN farmer_id INT,
                    ADD COLUMN description TEXT,
                    ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        
        // Refresh the page to use the new column
        header("Location: dashboard.php");
        exit();
    } catch(PDOException $e) {
        // Log the error but continue with the page
        error_log("Error adding columns: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 80%;
        margin: 50px auto;
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    h2 {
        text-align: center;
        color: #333;
    }
    nav {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
    }
    nav a {
        text-decoration: none;
        padding: 12px 25px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }
    nav a:hover {
        background-color: #45a049;
    }
    .logout {
        background-color: #f44336;
    }
    .logout:hover {
        background-color: #e53935;
    }
    footer {
        text-align: center;
        margin-top: 50px;
        font-size: 14px;
        color: #777;
    }
    .notifications, .product-requests {
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        border-radius: 8px;
        margin-top: 30px;
    }
    .notification-item, .product-item {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        border-left: 5px solid #4CAF50;
        transition: background-color 0.3s ease;
    }
    .notification-item.new {
        background-color: #e1f7d5;
    }
    .notification-item.read {
        background-color: #f1f1f1;
    }
    .notification-item strong, .product-item strong {
        font-weight: bold;
    }
    .notification-item a, .product-item a {
        text-decoration: none;
        color: #007bff;
    }
    .notification-item a:hover, .product-item a:hover {
        text-decoration: underline;
    }
    .order-summary {
        margin-top: 30px;
        padding: 10px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
        text-align: center; 
        padding: 12px; 
        font-weight: bold; 
    }
    td {
        text-align: center;
    }
    a {
        text-decoration: none;
        color: #007bff;
    }
    a:hover {
        text-decoration: underline;
    }
    hr {
        border: 0;
        height: 1px;
        background-color: #ddd;
        margin: 30px 0;
    }
    .action-btn-container {
        display: flex;
        gap: 10px;
        justify-content: center; 
    }

    .action-btn {
        padding: 8px 15px; 
        font-size: 14px;
        background-color: #4CAF50; 
        color: white;
        border: none;
        border-radius: 4px; 
        cursor: pointer;
        text-decoration: none;
        width: auto;
        min-width: 100px;
        text-align: center;
    }

    .action-btn:hover {
        background-color: #45a049;
    }

    .reject-btn {
        background-color: #f44336;
    }

    .reject-btn:hover {
        background-color: #e53935;
    }
    .product-requests {
        margin-top: 30px;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
    .product-requests h2 {
        color: #2c3e50;
        font-size: 24px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #eee;
    }
    .requests-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .request-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e0e0e0;
        overflow: hidden;
    }
    .request-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .request-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }
    .request-header h3 {
        margin: 0;
        color: #2c3e50;
        font-size: 18px;
        font-weight: 600;
    }
    .request-body {
        padding: 20px;
    }
    .request-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }
    .info-item {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #eee;
    }
    .info-item strong {
        display: block;
        color: #666;
        font-size: 12px;
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    .info-item span {
        color: #2c3e50;
        font-size: 14px;
        font-weight: 500;
    }
    .request-description {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        border: 1px solid #eee;
    }
    .request-description strong {
        display: block;
        color: #666;
        font-size: 12px;
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    .request-description p {
        color: #2c3e50;
        margin: 0;
        font-size: 14px;
        line-height: 1.5;
    }
    .request-footer {
        padding: 15px 20px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    .request-date {
        color: #666;
        font-size: 13px;
        margin-right: auto;
    }
    .request-actions {
        display: flex;
        gap: 10px;
        margin-left: auto;
    }
    .action-button {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        min-width: 120px;
    }
    .approve-btn {
        background: #28a745;
        color: white;
    }
    .approve-btn:hover {
        background: #218838;
    }
    .reject-btn {
        background: #dc3545;
        color: white;
    }
    .reject-btn:hover {
        background: #c82333;
    }
    .no-requests {
        text-align: center;
        padding: 40px 20px;
        color: #666;
        font-size: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px dashed #ddd;
    }
    .notifications {
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        border-radius: 12px;
        margin-top: 30px;
    }

    .notifications h2 {
        color: #2c3e50;
        font-size: 24px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #eee;
    }

    .notification-item {
        background: #f8f9fa;
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 8px;
        border-left: 4px solid #4CAF50;
        transition: all 0.3s ease;
    }

    .notification-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .notification-item.new {
        background-color: #e8f5e9;
        border-left-color: #4CAF50;
    }

    .notification-item.read {
        background-color: #f5f5f5;
        border-left-color: #9e9e9e;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .notification-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 16px;
    }

    .notification-time {
        color: #666;
        font-size: 13px;
    }

    .notification-content {
        color: #333;
        margin: 10px 0;
        line-height: 1.5;
    }

    .notification-details {
        background: #fff;
        padding: 15px;
        border-radius: 6px;
        margin-top: 10px;
        border: 1px solid #eee;
    }

    .notification-details p {
        margin: 5px 0;
        color: #555;
    }

    .notification-details strong {
        color: #333;
    }

    .notification-actions {
        margin-top: 15px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .notification-actions a {
        padding: 8px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .mark-read-btn {
        background: #e0e0e0;
        color: #333;
    }

    .mark-read-btn:hover {
        background: #d0d0d0;
    }

    .view-order-btn {
        background: #4CAF50;
        color: white;
    }

    .view-order-btn:hover {
        background: #45a049;
    }

    .no-notifications {
        text-align: center;
        padding: 30px;
        color: #666;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px dashed #ddd;
    }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <nav>
            <a href="manage_products.php">Manage Products</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
        <hr>
        
        <!-- Notifications Section -->
        <div class="notifications">
            <h2>Recent Notifications</h2>
            <div id="notification-container">
                <?php if (empty($notifications)): ?>
                    <div class="no-notifications">
                        <p>No new notifications</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?php echo $notification['status']; ?>" id="notification-<?php echo $notification['id']; ?>">
                            <div class="notification-header">
                                <div class="notification-title">
                                    <?php if ($notification['order_id']): ?>
                                        New Order #<?php echo $notification['order_id']; ?>
                                    <?php else: ?>
                                        System Notification
                                    <?php endif; ?>
                                </div>
                                <div class="notification-time">
                                    <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                                </div>
                            </div>
                            
                            <div class="notification-content">
                                <?php echo htmlspecialchars($notification['message']); ?>
                            </div>

                            <?php if ($notification['order_id']): ?>
                                <div class="notification-details">
                                    <p><strong>Customer:</strong> <?php echo htmlspecialchars($notification['customer_name']); ?></p>
                                    <p><strong>Total Amount:</strong> ₹<?php echo number_format($notification['total_cost'], 2); ?></p>
                                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($notification['payment_method']); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="notification-actions">
                                <?php if ($notification['order_id']): ?>
                                    <a href="view_order.php?id=<?php echo $notification['order_id']; ?>" class="view-order-btn">View Order</a>
                                <?php endif; ?>
                                <?php if ($notification['status'] == 'unread'): ?>
                                    <a href="javascript:void(0)" onclick="markAsRead(<?php echo $notification['id']; ?>)" class="mark-read-btn">Mark as Read</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Requests Section -->
        <div class="product-requests">
            <h2>Pending Product Requests</h2>
            <?php if (empty($product_requests)): ?>
                <div class="no-requests">
                    <p>No pending product requests at the moment</p>
                </div>
            <?php else: ?>
                <div class="requests-grid">
                    <?php foreach ($product_requests as $request): ?>
                        <div class="request-card" id="request-<?php echo $request['id']; ?>">
                            <div class="request-header">
                                <h3><?php echo htmlspecialchars($request['product_name']); ?></h3>
                            </div>
                            <div class="request-body">
                                <div class="request-info">
                                    <div class="info-item">
                                        <strong>Farmer Name</strong>
                                        <span><?php echo htmlspecialchars($request['farmer_name']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Phone Number</strong>
                                        <span><?php echo htmlspecialchars($request['phone_number']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Price per Kg</strong>
                                        <span>₹<?php echo number_format($request['price_per_kg'], 2); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Quantity</strong>
                                        <span><?php echo number_format($request['quantity'], 2); ?> kg</span>
                                    </div>
                                </div>
                                <?php if (!empty($request['description'])): ?>
                                    <div class="request-description">
                                        <strong>Description</strong>
                                        <p><?php echo htmlspecialchars($request['description']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="request-footer">
                                <?php if (isset($request['created_at']) && $request['created_at']): ?>
                                    <div class="request-date">
                                        <?php echo date('M d, Y H:i', strtotime($request['created_at'])); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="request-actions">
                                    <a href="add_product.php?id=<?php echo $request['id']; ?>" class="action-button approve-btn">Approve</a>
                                    <button onclick="rejectRequest(<?php echo $request['id']; ?>)" class="action-button reject-btn">Reject</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function markAsRead(notificationId) {
        fetch('mark_notification_read.php?id=' + notificationId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notification = document.getElementById('notification-' + notificationId);
                    if (notification) {
                        notification.classList.remove('unread');
                        notification.classList.add('read');
                        const markAsReadLink = notification.querySelector('a');
                        if (markAsReadLink) {
                            markAsReadLink.remove();
                        }
                    }
                }
            });
    }

    function rejectRequest(requestId) {
        if (confirm('Are you sure you want to reject this request?')) {
            fetch('reject_product.php?id=' + requestId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add new notification to the container
                        const notificationContainer = document.getElementById('notification-container');
                        const newNotification = document.createElement('div');
                        newNotification.className = 'notification-item unread';
                        newNotification.innerHTML = `
                            <p>${data.message}</p>
                            <small>Received: Just now</small>
                            <a href="javascript:void(0)" onclick="markAsRead(${data.notification_id})">Mark as Read</a>
                        `;
                        notificationContainer.insertBefore(newNotification, notificationContainer.firstChild);

                        // Remove the rejected request
                        const requestElement = document.getElementById('request-' + requestId);
                        if (requestElement) {
                            requestElement.remove();
                        }

                        // Show success message
                        alert('Request rejected successfully');
                    } else {
                        alert('Error rejecting request: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error rejecting request. Please try again.');
                });
        }
    }
    </script>
</body>
</html>
?>
        