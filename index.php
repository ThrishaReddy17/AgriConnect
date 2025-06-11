<?php
session_start();
if (isset($_POST['logout'])) {
    session_unset(); 
    session_destroy(); 
    header("Location: pages/login.php"); 
    exit(); 
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: pages/login.php");
    exit();
}

// Fetch products from the database with a search option
include 'includes/db.php';

// Fetch user's orders and notifications
if (isset($_SESSION['user_id'])) {
    // Fetch recent orders
    $stmt = $conn->prepare("
        SELECT o.*, 
               GROUP_CONCAT(CONCAT(oi.quantity, 'x ', p.name) SEPARATOR ', ') as items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch unread notifications
    $stmt = $conn->prepare("
        SELECT * FROM notifications 
        WHERE user_id = ? AND status = 'unread'
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$search_query = isset($_GET['search']) ? $_GET['search'] : ''; // Get search term from URL
if ($search_query) {
    // Use a case-insensitive search (like operator) in the database query
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :search OR description LIKE :search");
    $stmt->bindValue(':search', '%' . $search_query . '%', PDO::PARAM_STR);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Default query to fetch all products if no search term is provided
    $stmt = $conn->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Welcome to Our Store🌿</h1>
            <nav>
                <a href="pages/login.php">🔓Login</a>
                <a href="pages/register.php">🔑Register</a>
                <a href="pages/cart.php">🛒Cart</a>
                <!-- Logout button -->
                <form method="POST" style="display: inline;">
                    <button type="submit" name="logout" class="logout-button">Logout</button>
                </form>
            </nav>
        </div>
    </header>
    
    <!-- Search Form Below the Navbar -->
    <div class="search-container">
        <form method="GET" action="" class="search-form">
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search_query); ?>">
            <button type="submit" class="search-button">Search</button>
        </form>
    </div>

    <div class="main-container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-dashboard">
                <!-- Notifications Section -->
                <?php if (!empty($notifications)): ?>
                    <div class="notifications-section">
                        <h3>Recent Notifications</h3>
                        <div class="notifications-list">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <?php echo htmlspecialchars($notification['message']); ?>
                                    </div>
                                    <div class="notification-time">
                                        <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Recent Orders Section -->
                <?php if (!empty($recent_orders)): ?>
                    <div class="orders-section">
                        <h3>Recent Orders</h3>
                        <div class="orders-list">
                            <?php foreach ($recent_orders as $order): ?>
                                <div class="order-item">
                                    <div class="order-header">
                                        <span class="order-id">Order #<?php echo $order['id']; ?></span>
                                        <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                    <div class="order-details">
                                        <p class="order-items"><?php echo htmlspecialchars($order['items']); ?></p>
                                        <p class="order-total">Total: ₹<?php echo number_format($order['total_cost'], 2); ?></p>
                                        <p class="order-date">Ordered on: <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                                    </div>
                                    <a href="pages/order_confirmation.php?order_id=<?php echo $order['id']; ?>" class="view-order-btn">View Details</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <main>
            <h2>Organic Products</h2>
            <div class="product-list">
                <?php if (empty($products)) : ?>
                    <p>No products found matching your search.</p>
                <?php else : ?>
                    <?php foreach ($products as $product) : ?>
                        <div class="product">
                            <h3><?= htmlspecialchars($product['name']); ?></h3>
                            <p>Price: $<?= number_format($product['price'], 2); ?></p>
                            <p><?= htmlspecialchars($product['description']); ?></p>
                            <?php if (!empty($product['image'])) : ?>
                                <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                            <?php endif; ?>
                            <form method="POST" action="pages/cart.php">
                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <footer>
        <p>&copy; <?= date('Y'); ?> Online Store. All rights reserved.</p>
    </footer>

    <style>
        .user-dashboard {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .notifications-section,
        .orders-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            padding: 20px;
        }

        .notifications-section h3,
        .orders-section h3 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .notification-item {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #4CAF50;
        }

        .notification-content {
            color: #333;
            margin-bottom: 5px;
        }

        .notification-time {
            color: #666;
            font-size: 0.9em;
        }

        .order-item {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #eee;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .order-id {
            font-weight: bold;
            color: #2c3e50;
        }

        .order-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1e7dd; color: #0f5132; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .order-details {
            margin: 10px 0;
        }

        .order-items {
            color: #666;
            margin-bottom: 5px;
        }

        .order-total {
            font-weight: bold;
            color: #2c3e50;
            margin: 5px 0;
        }

        .order-date {
            color: #666;
            font-size: 0.9em;
            margin: 5px 0;
        }

        .view-order-btn {
            display: inline-block;
            padding: 8px 16px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            transition: background-color 0.3s;
        }

        .view-order-btn:hover {
            background: #45a049;
        }
    </style>
</body>
</html>
