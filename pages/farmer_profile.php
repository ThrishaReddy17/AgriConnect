<?php
session_start();

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'farmer') {
    header("Location: login.php");
    exit();
}

$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$user_id = $_SESSION['user_id'];
include('../includes/db.php');

// Fetch profile information
$stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Debug output
error_log("Profile data: " . print_r($profile, true));

// If profile doesn't exist, redirect to profile form
if (!$profile) {
    header("Location: profile_form.php");
    exit();
}

// Fetch user's email
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch product requests history
$stmt = $conn->prepare("SELECT * FROM product_requests WHERE farmer_name = ? ORDER BY request_date DESC");
$stmt->execute([$profile['full_name']]);
$product_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch stubble burning requests for this farmer
$stubble_sql = "SELECT * FROM stubbleburning WHERE LOWER(farmer_name) = LOWER(?) ORDER BY id DESC";
$stubble_stmt = $conn->prepare($stubble_sql);
$stubble_stmt->execute([$profile['full_name']]);
$stubble_requests = $stubble_stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug output
error_log("Stubble requests query: " . $stubble_sql);
error_log("Farmer name used: " . $profile['full_name']);
error_log("Stubble requests found: " . print_r($stubble_requests, true));

?>

<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .profile-header h1 {
            margin: 0;
            color: #333;
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        .nav-buttons a {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .dashboard-btn {
            background-color: #4CAF50;
            color: white;
        }
        .logout-btn {
            background-color: #f44336;
            color: white;
        }
        .profile-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .profile-details, .farm-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }
        .profile-details h2, .farm-details h2 {
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-item label {
            font-weight: bold;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }
        .detail-item span {
            color: #333;
        }
        .product-requests {
            margin-top: 30px;
        }
        .product-requests h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        @media (max-width: 768px) {
            .profile-section {
                grid-template-columns: 1fr;
            }
            .nav-buttons {
                flex-direction: column;
            }
            table {
                display: block;
                overflow-x: auto;
            }
        }
        .request-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .request-table th,
        .request-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .request-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-accepted { background-color: #28a745; color: #fff; }
        .status-rejected { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <h1>Farmer Profile</h1>
            <div class="nav-buttons">
                <a href="farmer_dashboard.php" class="dashboard-btn">Dashboard</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="profile-section">
            <div class="profile-details">
                <h2>Personal Information</h2>
                <div class="detail-item">
                    <label>Full Name</label>
                    <span><?php echo htmlspecialchars($profile['full_name']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Phone Number</label>
                    <span><?php echo htmlspecialchars($profile['phone_number']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Address</label>
                    <span><?php echo htmlspecialchars($profile['address']); ?></span>
                </div>
            </div>

            <div class="farm-details">
                <h2>Farm Information</h2>
                <div class="detail-item">
                    <label>Agricultural Land</label>
                    <span><?php echo htmlspecialchars($profile['agricultural_land']); ?> acres</span>
                </div>
                <div class="detail-item">
                    <label>Farm Size</label>
                    <span><?php echo htmlspecialchars($profile['farm_size']); ?></span>
                </div>
            </div>
        </div>

        <!-- Stubble Burning Requests Section -->
        <div class="requests-section">
            <h2>Stubble Burning Requests</h2>
            <?php if (empty($stubble_requests)): ?>
                <p>No stubble burning requests found.</p>
            <?php else: ?>
                <table class="request-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Acres</th>
                            <th>Quantity (Tons)</th>
                            <th>Collection Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stubble_requests as $request): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($request['collection_date'])); ?></td>
                                <td><?php echo htmlspecialchars($request['location']); ?></td>
                                <td><?php echo number_format($request['acres_of_land'], 2); ?></td>
                                <td><?php echo number_format($request['stubble_quantity'], 2); ?></td>
                                <td><?php echo date('d M Y', strtotime($request['collection_date'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($request['status']); ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Product Requests Section -->
        <div class="requests-section">
            <h2>Product Requests</h2>
            <?php if (empty($product_requests)): ?>
                <p>No product requests found.</p>
            <?php else: ?>
                <table class="request-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product Name</th>
                            <th>Price per Kg</th>
                            <th>Quantity (Kg)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($product_requests as $request): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($request['request_date'])); ?></td>
                                <td><?php echo htmlspecialchars($request['product_name']); ?></td>
                                <td>₹<?php echo number_format($request['price_per_kg'], 2); ?></td>
                                <td><?php echo number_format($request['quantity'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($request['status']); ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 