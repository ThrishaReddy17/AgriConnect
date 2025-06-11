<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user's cart items along with product details (price)
$stmt = $conn->prepare("SELECT c.*, p.price, p.name, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total cost
$total_cost = 0;
foreach ($cart_items as $cart_item) {
    $total_cost += $cart_item['price'] * $cart_item['quantity'];
}

// Handle the order placement when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method'];

    if (empty($name) || empty($address) || empty($payment_method)) {
        die("All fields are required.");
    }

    $stmt = $conn->prepare("INSERT INTO orders (user_id, name, address, total_cost, status, payment_method, created_at) VALUES (?, ?, ?, ?, 'pending', ?, NOW())");
    $stmt->execute([$user_id, $name, $address, $total_cost, $payment_method]);

    $order_id = $conn->lastInsertId();

    // Create order items
    foreach ($cart_items as $cart_item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$order_id, $cart_item['product_id'], $cart_item['quantity']]);
    }

    // Create admin notification for the new order
    $order_details = [];
    foreach ($cart_items as $item) {
        $order_details[] = $item['quantity'] . " x " . $item['name'];
    }
    $order_summary = implode(", ", $order_details);
    $notification_message = "New order #$order_id from $name: $order_summary (Total: ₹" . number_format($total_cost, 2) . ")";
    
    // Create notification for admin
    $stmt = $conn->prepare("INSERT INTO notifications (message, status, order_id) VALUES (?, 'unread', ?)");
    $stmt->execute([$notification_message, $order_id]);

    // Create notification for the customer
    $customer_message = "Your order #$order_id has been placed successfully. We will notify you when it's processed.";
    $stmt = $conn->prepare("INSERT INTO notifications (message, status, order_id, user_id) VALUES (?, 'unread', ?, ?)");
    $stmt->execute([$customer_message, $order_id, $user_id]);

    // Clear the cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
        }
        .form-container {
            width: 48%;
        }
        h2 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
            width: 100%;
        }
        label {
            font-size: 1.1em;
            margin-bottom: 5px;
            display: block;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            margin-top: 20px;
        }
        button:hover {
            background-color: #218838;
        }
        #payment-section {
            text-align: center;
            margin-top: 20px;
            display: none;
        }
        #map {
            height: 300px;
            width: 100%;
            margin-bottom: 10px;
        }
        #payment-section img {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function togglePayment() {
            var paymentMethod = document.getElementById('payment_method').value;
            var paymentSection = document.getElementById('payment-section');
            if (paymentMethod === 'UPI QR Code') {
                paymentSection.style.display = 'block';
            } else {
                paymentSection.style.display = 'none';
            }
        }

        function initMap() {
            var map = L.map('map').setView([20, 78], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            var marker = L.marker([20, 78], { draggable: true }).addTo(map);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;
                    map.setView([lat, lon], 15);
                    marker.setLatLng([lat, lon]);
                });
            }

            marker.on('dragend', function() {
    var position = marker.getLatLng();
    document.getElementById('address').value = `Latitude: ${position.lat}, Longitude: ${position.lng}`;
});

        }

        window.onload = function() {
            togglePayment();
            initMap();
        };
    </script>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2>Shipping Details</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="address">Shipping Address:</label>
                <textarea name="address" id="address" rows="4" required></textarea>
                <div id="map"></div>
            </div>
        </div>
        <div class="form-container">
            <h2>Payment Details</h2>
            <div class="form-group">
                <label for="payment_method">Payment Method:</label>
                <select name="payment_method" id="payment_method" required onchange="togglePayment()">
                    <option value="UPI QR Code">UPI QR Code</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                </select>
            </div>
            <div id="payment-section">
                <h3>Scan & Pay via UPI</h3>
                <img src="http://localhost/ecommerce/images/qr2.jpg" alt="UPI QR Code" width="200">
            </div>
            <button type="submit" name="place_order">Place Order</button>
        </form>
    </div>
</div>
</body>



</html>