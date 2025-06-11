<?php
include 'includes/db.php';

// Admin credentials
$admin_email = 'admin@agriconnect.com';
$admin_password = 'Admin@123';

echo "<h2>Checking Admin Account</h2>";

try {
    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$admin_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<p style='color: green;'>✓ User found in database</p>";
        echo "<p>User details:</p>";
        echo "<ul>";
        echo "<li>ID: " . $user['id'] . "</li>";
        echo "<li>Email: " . $user['email'] . "</li>";
        echo "<li>Role: " . $user['role'] . "</li>";
        echo "</ul>";

        // Test password
        if (password_verify($admin_password, $user['password'])) {
            echo "<p style='color: green;'>✓ Password verification successful</p>";
        } else {
            echo "<p style='color: red;'>✗ Password verification failed</p>";
            
            // Create new password hash
            $new_hash = password_hash($admin_password, PASSWORD_DEFAULT);
            echo "<p>Attempting to update password...</p>";
            
            // Update password
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->execute([$new_hash, $admin_email]);
            
            if ($update->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Password updated successfully</p>";
                echo "<p>New password hash: " . $new_hash . "</p>";
            } else {
                echo "<p style='color: red;'>✗ Failed to update password</p>";
            }
        }

        // Check role
        if (strtolower($user['role']) === 'admin') {
            echo "<p style='color: green;'>✓ Role is correct (admin)</p>";
        } else {
            echo "<p style='color: red;'>✗ Role is incorrect. Current role: " . $user['role'] . "</p>";
            echo "<p>Attempting to update role...</p>";
            
            // Update role
            $update = $conn->prepare("UPDATE users SET role = 'admin' WHERE email = ?");
            $update->execute([$admin_email]);
            
            if ($update->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Role updated to 'admin'</p>";
            } else {
                echo "<p style='color: red;'>✗ Failed to update role</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>✗ No user found with email: " . htmlspecialchars($admin_email) . "</p>";
        echo "<p>Attempting to create admin user...</p>";
        
        // Create admin user
        $hash = password_hash($admin_password, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'admin')");
        $insert->execute([$admin_email, $hash]);
        
        if ($insert->rowCount() > 0) {
            echo "<p style='color: green;'>✓ Admin user created successfully</p>";
            echo "<p>New user details:</p>";
            echo "<ul>";
            echo "<li>Email: " . $admin_email . "</li>";
            echo "<li>Password: " . $admin_password . "</li>";
            echo "<li>Role: admin</li>";
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>✗ Failed to create admin user</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}
?> 