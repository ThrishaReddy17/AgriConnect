<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$order_id = intval($_GET['id']);

// Fetch order details
$stmt = $conn->prepare("
    SELECT o.*, u.email as customer_email, p.phone_number
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN profiles p ON o.user_id = p.user_id
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: dashboard.php");
    exit();
}

// Fetch order items
$stmt = $conn->prepare("
    SELECT oi.*, p.name, p.price, p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    error_log("Updating order #$order_id status to: " . $new_status);
    
    try {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $result = $stmt->execute([$new_status, $order_id]);
        
        if ($result) {
            error_log("Status update successful");
            // Create notification for the customer
            $message = "Your order #$order_id status has been updated to: " . ucfirst($new_status);
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, status, order_id) VALUES (?, ?, 'unread', ?)");
            $stmt->execute([$order['user_id'], $message, $order_id]);
            error_log("Notification created for user #" . $order['user_id']);
        } else {
            error_log("Status update failed");
        }
    } catch (PDOException $e) {
        error_log("Database error during status update: " . $e->getMessage());
    }
    
    header("Location: view_order.php?id=$order_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?php echo $order_id; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .order-header {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .order-info h3 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .order-info p {
            margin: 8px 0;
            color: #555;
        }
        .order-info strong {
            color: #333;
        }
        .order-items {
            margin-top: 30px;
        }
        .order-items h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #eee;
        }
        .items-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        .items-table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .status-form {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .status-form select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
        }
        .status-form button {
            padding: 8px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .status-form button:hover {
            background: #45a049;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }
        .back-link:hover {
            color: #333;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .total-section p {
            font-size: 18px;
            color: #2c3e50;
            margin: 5px 0;
        }
        .total-section strong {
            font-size: 24px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Details #<?php echo $order_id; ?></h2>
        
        <div class="order-header">
            <div class="order-info">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone_number'] ?? 'Not provided'); ?></p>
            </div>
            
            <div class="order-info">
                <h3>Shipping Information</h3>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
            </div>
            
            <div class="order-info">
                <h3>Order Status</h3>
                <form method="POST" class="status-form">
                    <select name="status">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_status" value="1">Update Status</button>
                </form>
            </div>
        </div>

        <div class="order-items">
            <h3>Order Items</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td>
                                <img src="../images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-section">
                <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_cost'], 2); ?></p>
            </div>
        </div>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html> 