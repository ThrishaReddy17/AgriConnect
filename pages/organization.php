<?php
session_start();
include '../includes/db.php';

// Check if the organization is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organization') {
    header("Location: login.php");
    exit();
}

$organization_id = $_SESSION['user_id'];

// Fetch organization profile details securely
$profile_stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$profile_stmt->execute([$organization_id]);
$profile = $profile_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch stubble burning data along with request status
$sql = "SELECT sb.* FROM stubbleburning sb ORDER BY sb.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$stubble_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Accept/Reject Action for Stubble Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['stubble_id'])) {
    $stubble_id = $_POST['stubble_id'];
    $action = $_POST['action'];
    $status = ($action == 'accept') ? 'Accepted' : 'Rejected';
    $update_stmt = $conn->prepare("UPDATE stubbleburning SET status = ? WHERE id = ?");
    $update_stmt->execute([$status, $stubble_id]);
    echo json_encode(['success' => true, 'status' => strtolower($status)]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .profile-section {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .profile-section h3 {
            color: #007bff;
        }
        .profile-details p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: capitalize;
        }
        .status-requested { color: #ffc107; }
        .status-accepted { color: #28a745; }
        .status-rejected { color: #dc3545; }
        .action-btn {
            padding: 8px 15px;
            border: 1px solid #888;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin: 2px;
            background: #fff;
        }
        .accept-btn { border-color: #28a745; color: #28a745; }
        .reject-btn { border-color: #dc3545; color: #dc3545; }
        .action-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .logout-btn {
            float: right;
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: -40px;
        }
        .logout-btn:hover {
            background-color: #b02a37;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Organization Dashboard</h2>
        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>

        <!-- Organization Profile Section -->
        <div class="profile-section">
            <h3>Organization Profile</h3>
            <div class="profile-details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($profile['organization_name'] ?? 'Not Available'); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($profile['phone_number'] ?? 'Not Available'); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($profile['address'] ?? 'Not Available'); ?></p>
            </div>
        </div>

        <!-- Available Stubble for Collection -->
        <h2>Available Stubble for Collection</h2>
        <table>
            <tr>
                <th>Farmer Name</th>
                <th>Phone</th>
                <th>Location</th>
                <th>Acres</th>
                <th>Stubble Quantity (Tons)</th>
                <th>Est. Collection Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($stubble_data as $row): ?>
                <tr id="row-<?php echo $row['id']; ?>">
                    <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['acres_of_land']); ?></td>
                    <td><?php echo htmlspecialchars($row['stubble_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['collection_date'] ?? $row['estimation_of_stubble']); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower($row['status'] ?? 'requested'); ?>">
                            <?php echo ucfirst($row['status'] ?? 'Requested'); ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!isset($row['status']) || strtolower($row['status']) == 'requested' || strtolower($row['status']) == 'pending'): ?>
                            <div class="action-container">
                                <button class="action-btn accept-btn" onclick="handleRequest(<?php echo $row['id']; ?>, 'accept')">Accept</button>
                                <button class="action-btn reject-btn" onclick="handleRequest(<?php echo $row['id']; ?>, 'reject')">Reject</button>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        function handleRequest(stubbleId, action) {
            if (!confirm(`Are you sure you want to ${action} this request?`)) return;
            const row = document.getElementById('row-' + stubbleId);
            const buttons = row.querySelectorAll('.action-btn');
            buttons.forEach(btn => btn.disabled = true);
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=' + action + '&stubble_id=' + stubbleId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statusBadge = row.querySelector('.status-badge');
                    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    statusBadge.className = 'status-badge status-' + data.status;
                    const actionContainer = row.querySelector('.action-container');
                    if (actionContainer) actionContainer.remove();
                } else {
                    alert('Error processing request.');
                    buttons.forEach(btn => btn.disabled = false);
                }
            })
            .catch(() => {
                alert('Error processing request.');
                buttons.forEach(btn => btn.disabled = false);
            });
        }
    </script>
</body>
</html>  