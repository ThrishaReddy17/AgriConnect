<?php
session_start();
include 'includes/db.php';

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

// Fetch unique stubble requests
$sql = "SELECT DISTINCT id, farmer_name, phone_number, location, acres_of_land, 
        stubble_quantity, collection_date, status 
        FROM stubbleburning 
        ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$stubble_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            max-width: 1200px;
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
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
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
        td {
            vertical-align: middle;
            text-align: center;
        }
        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin: 2px;
            transition: opacity 0.3s;
        }
        .action-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .accept-btn {
            background-color: #28a745;
            color: white;
        }
        .reject-btn {
            background-color: #dc3545;
            color: white;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: capitalize;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-accepted { background-color: #28a745; color: #fff; }
        .status-rejected { background-color: #dc3545; color: #fff; }
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

        <!-- Stubble Requests Section -->
        <div class="stubble-section">
            <h2>Stubble Burning Requests</h2>
            <?php if (empty($stubble_data)): ?>
                <p>No stubble burning requests available.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Farmer Name</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Acres</th>
                            <th>Stubble Quantity (Tons)</th>
                            <th>Collection Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stubble_data as $row): ?>
                            <tr id="request-<?php echo $row['id']; ?>">
                                <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo number_format($row['acres_of_land'], 2); ?></td>
                                <td><?php echo number_format($row['stubble_quantity'], 2); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['collection_date'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($row['status'] ?? 'pending'); ?>">
                                        <?php echo ucfirst($row['status'] ?? 'Pending'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!isset($row['status']) || $row['status'] == 'pending'): ?>
                                        <div class="action-container">
                                            <button class="action-btn accept-btn" onclick="handleRequest(<?php echo $row['id']; ?>, 'accept')">Accept</button>
                                            <button class="action-btn reject-btn" onclick="handleRequest(<?php echo $row['id']; ?>, 'reject')">Reject</button>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function handleRequest(stubbleId, action) {
        if (!confirm(`Are you sure you want to ${action} this request?`)) {
            return;
        }

        // Disable both buttons
        const buttons = document.querySelectorAll(`#request-${stubbleId} .action-btn`);
        buttons.forEach(button => button.disabled = true);
        
        fetch('handle_stubble_request.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `stubble_id=${stubbleId}&action=${action}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const requestElement = document.getElementById(`request-${stubbleId}`);
                if (requestElement) {
                    // Update status badge
                    const statusBadge = requestElement.querySelector('.status-badge');
                    if (statusBadge) {
                        const newStatus = action === 'accept' ? 'accepted' : 'rejected';
                        statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        statusBadge.className = `status-badge status-${newStatus}`;
                    }
                    
                    // Remove action buttons
                    const actionContainer = requestElement.querySelector('.action-container');
                    if (actionContainer) {
                        actionContainer.remove();
                    }
                }
                alert(data.message || `Request ${action}ed successfully`);
            } else {
                alert(data.message || 'Error processing request');
                // Re-enable buttons on error
                buttons.forEach(button => button.disabled = false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error processing request. Please try again.');
            // Re-enable buttons on error
            buttons.forEach(button => button.disabled = false);
        });
    }
    </script>
</body>
</html> 