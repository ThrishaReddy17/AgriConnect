<?php
include '../includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: reset_password.php");
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: reset_password.php");
        exit();
    }

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = "No admin account found with this email!";
        header("Location: reset_password.php");
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hashed_password, $email]);

    $_SESSION['message'] = "Password reset successfully! Please login.";
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            background-image:url('https://i0.wp.com/picjumbo.com/wp-content/uploads/simple-black-dark-fall-background-free-image.jpeg?w=2210&quality=70');
            background-size: cover;
            background-position: center center;
        }
        .reset-container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #dc3545;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <div class="reset-container">
        <h2>Reset Password</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<p style='color:red; text-align:center;'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['message'])) {
            echo "<p style='color:green; text-align:center;'>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        }
        ?>
        <form method="POST">
            <label for="email">Registered Email</label>
            <input type="email" name="email" id="email" required>

            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit">Reset Password</button>
        </form>
    </div>

</body>
</html>
