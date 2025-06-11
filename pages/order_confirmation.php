<?php 
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

// Fetch the order ID from the URL query parameter
if (isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];

    // Fetch the order details
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Fetch the order items
        $stmt = $conn->prepare("SELECT oi.*, p.name, p.price, p.image FROM order_items oi
                                JOIN products p ON oi.product_id = p.id
                                WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch the user's phone number
        $stmt = $conn->prepare("SELECT phone_number FROM profiles WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        $phone_number = $profile ? $profile['phone_number'] : 'Not Available';

        // Calculate the total cost
        $total_cost = $order['total_cost'];
    } else {
        // If no order found, redirect to home page
        header("Location: index.php");
        exit();
    }
} else {
    // If no order ID is provided, redirect to home page
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 900px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .order-details {
            margin-bottom: 30px;
        }
        .order-details p {
            font-size: 1.2em;
        }
        .order-items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .order-items-table th, .order-items-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .order-items-table th {
            background-color: #f2f2f2;
        }
        .order-items-table img {
            max-width: 50px;
            margin-right: 10px;
        }
        .total-cost {
            font-size: 1.5em;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
        .back-to-home {
            text-align: center;
            margin-top: 30px;
        }
        .back-to-home a {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.1em;
        }
        .back-to-home a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Confirmation</h2>
        
        <div class="order-details">
            <p><strong>Order ID:</strong> <?= htmlspecialchars($order['id']); ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($order['name']); ?></p>
            <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['address']); ?></p>
            <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']); ?></p>
            <p><strong>Status:</strong> <?= ucfirst($order['status']); ?></p>
            <p><strong>Phone Number:</strong> <?= htmlspecialchars($phone_number); ?></p>
        </div>

        <h3>Order Items:</h3>
        <table class="order-items-table">
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
                        <img src="../images/<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>"> 
                        <?= htmlspecialchars($item['name']); ?>
                    </td>
                    <td>$<?= number_format($item['price'], 2); ?></td>
                    <td><?= htmlspecialchars($item['quantity']); ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-cost">
            <p>Total Cost: $<?= number_format($total_cost, 2); ?></p>
        </div>

        <div class="back-to-home">
            <a href="../index.php">Back to Shop</a>
        </div>
    </div>
</body>
</html>
