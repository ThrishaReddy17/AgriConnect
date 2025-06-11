<?php
include '../includes/db.php';

// Admin email to check
$admin_email = 'admin@agriconnect.com';

echo "<h2>Debugging Admin User</h2>";

// Check all users with this email
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$admin_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "<h3>User Found:</h3>";
    echo "<pre>";
    echo "ID: " . $user['id'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Password Hash: " . $user['password'] . "\n";
    echo "</pre>";

    // Test password verification
    $test_password = 'Admin@123';
    if (password_verify($test_password, $user['password'])) {
        echo "<p style='color: green;'>Password verification successful!</p>";
    } else {
        echo "<p style='color: red;'>Password verification failed!</p>";
    }

    // Check if role is exactly 'admin'
    if ($user['role'] === 'admin') {
        echo "<p style='color: green;'>Role is exactly 'admin'</p>";
    } else {
        echo "<p style='color: red;'>Role is not exactly 'admin'. Current role: '" . $user['role'] . "'</p>";
    }
} else {
    echo "<p style='color: red;'>No user found with email: " . htmlspecialchars($admin_email) . "</p>";
}

// Show all users with admin role
echo "<h3>All Users with Admin Role:</h3>";
$stmt = $conn->query("SELECT id, email, role FROM users WHERE role LIKE '%admin%'");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($admins) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Email</th><th>Role</th></tr>";
    foreach ($admins as $admin) {
        echo "<tr>";
        echo "<td>" . $admin['id'] . "</td>";
        echo "<td>" . $admin['email'] . "</td>";
        echo "<td>" . $admin['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users found with admin role</p>";
}

// Show database table structure
echo "<h3>Users Table Structure:</h3>";
$stmt = $conn->query("DESCRIBE users");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($columns);
echo "</pre>";
?> 