<?php
include '../includes/db.php';

// Admin credentials
$admin_email = 'admin@agriconnect.com';
$admin_password = 'Admin@123';
$admin_role = 'admin';

// Check if admin already exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$admin_email]);
$existing_admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$existing_admin) {
    // Create admin user
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$admin_email, $hashed_password, $admin_role]);
        echo "Admin user created successfully!<br>";
        echo "Email: " . $admin_email . "<br>";
        echo "Password: " . $admin_password . "<br>";
        echo "Role: " . $admin_role . "<br>";
    } catch (PDOException $e) {
        echo "Error creating admin user: " . $e->getMessage();
    }
} else {
    // Update admin password if needed
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ? AND role = ?");
    
    try {
        $stmt->execute([$hashed_password, $admin_email, $admin_role]);
        echo "Admin password updated successfully!<br>";
        echo "Email: " . $admin_email . "<br>";
        echo "New Password: " . $admin_password . "<br>";
        echo "Role: " . $admin_role . "<br>";
    } catch (PDOException $e) {
        echo "Error updating admin password: " . $e->getMessage();
    }
}
?> 