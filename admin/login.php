<?php
include '../includes/db.php';
session_start();

// Clear any existing session data
session_unset();
session_destroy();
session_start();

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Debug information
    error_log("Login attempt - Email: " . $email);

    // First check if user exists with this email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        error_log("User found - ID: " . $user['id'] . ", Role: " . $user['role']);
        
        // Check if user has admin role (case insensitive)
        if (strtolower($user['role']) === 'admin') {
            error_log("Admin role verified");
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                error_log("Password verified successfully");
                
                // Set session variables
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['role'] = 'admin';
                $_SESSION['email'] = $user['email'];
                
                error_log("Session variables set - admin_id: " . $_SESSION['admin_id'] . ", role: " . $_SESSION['role']);
                
                // Verify session was set
                if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
                    error_log("Session verified, redirecting to dashboard");
                    header("Location: dashboard.php");
                    exit();
                } else {
                    error_log("Session variables not set properly");
                    $error_message = "Session error. Please try again.";
                }
            } else {
                error_log("Password verification failed");
                $error_message = "Incorrect password. Please try again.";
            }
        } else {
            error_log("User does not have admin role. Current role: " . $user['role']);
            $error_message = "This account does not have admin privileges.";
        }
    } else {
        error_log("No user found with email: " . $email);
        $error_message = "No account found with this email!";
    }
}

// Debug information
if (isset($_SESSION)) {
    error_log("Current session data: " . print_r($_SESSION, true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            background-image: url('https://i0.wp.com/picjumbo.com/wp-content/uploads/simple-black-dark-fall-background-free-image.jpeg?w=2210&quality=70');
            background-size: cover;
            background-position: center center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        .forgot-password a {
            color: #666;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="login">Login</button>

            <div class="forgot-password">
                <a href="reset_password.php">Forgot Password?</a>
            </div>
        </form>
    </div>
</body>
</html>
