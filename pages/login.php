<?php
include('../includes/db.php');  // Include the database connection
session_start();

// Set the default language if it's not already set
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en'; // Default language is English
}

// Add this at the top of the file after session_start() to debug
if (isset($_SESSION['role'])) {
    error_log("Current session role: " . $_SESSION['role']);
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Check if the user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // Check if profile is completed
                $stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $profile = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$profile) {
                    // If profile is not completed, redirect to profile form
                    $_SESSION['needs_profile'] = true;
                    echo "<script>alert('Please complete your profile first.'); window.location.href = 'profile_form.php';</script>";
                    exit();
                }

                // Debug information
                error_log("User Role: " . $user['role']);
                error_log("Session Role: " . $_SESSION['role']);

                // Redirect based on role after profile completion
                if (strtolower($user['role']) === 'farmer') {
                    echo "<script>alert('Login successful!'); window.location.href = 'farmer_dashboard.php';</script>";
                } else if (strtolower($user['role']) === 'organization') {
                    echo "<script>alert('Login successful!'); window.location.href = 'organization.php';</script>";
                } else {
                    echo "<script>alert('Login successful!'); window.location.href = '../index.php';</script>";
                }
                exit();
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-image: url('https://previews.123rf.com/images/singkham/singkham1810/singkham181000022/111277524-agriculture-and-gardening-equipment-background-for-template-and-slide-presentation-design.jpg');
            background-size: cover;
            background-position: center center;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(5px);
            transition: transform 0.3s ease-in-out;
            position: relative;
        }
        .login-container:hover {
            transform: scale(1.05);
        }
        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 20px;
            font-size: 1.8em;
            font-weight: bold;
        }
        label {
            font-size: 1.1em;
            margin-bottom: 5px;
            display: block;
            color: #111;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1.1em;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #28a745;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0f9d58 0%, #0f766e 100%);
            color: white;
            font-size: 1.2em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }
        .error-message {
            color: #e74c3c;
            font-size: 1em;
            text-align: center;
            margin-top: 10px;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(0, 0, 0, 0.3);
            color: white;
            padding: 10px 20px;
            font-size: 1.1em;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: rgba(0, 0, 0, 0.5);
        }
        .password-container {
            position: relative;
        }
        .password-container input[type="password"] {
            padding-right: 40px;
            width: 100%;
            box-sizing: border-box;
        }
        .password-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-link a {
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Back Button -->
    <a href="starting.php" class="back-button" id="back-button">Back</a>

    <div class="login-container">
        <h2 id="login-title">Login</h2>
        <form method="POST">
            <label id="email-label">Email:</label>
            <input type="email" name="email" required>

            <label id="password-label">Password:</label>
            <div class="password-container">
                <input type="password" name="password" id="password" required>
                <i class="eye-icon" id="togglePassword" onclick="togglePassword()">&#128065;</i>
            </div>

            <button type="submit" name="login" id="login-btn">Login</button>
        </form>
        <div class="register-link">
            <span id="no-account">Don't have an account?</span>
            <a href="register.php" id="register-link">Register here</a>
        </div>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </div>

    <script>
        const translations = {
            en: {
                "login-title": "Login",
                "email-label": "Email",
                "password-label": "Password",
                "login-btn": "Login",
                "back-button": "Back",
                "no-account": "Don't have an account?",
                "register-link": "Register here"
            },
            hi: {
                "login-title": "लॉग इन करें",
                "email-label": "ईमेल",
                "password-label": "पासवर्ड",
                "login-btn": "लॉग इन करें",
                "back-button": "वापस",
                "no-account": "खाता नहीं है?",
                "register-link": "यहां पंजीकरण करें"
            },
            te: {
                "login-title": "లాగిన్",
                "email-label": "ఇమెయిల్",
                "password-label": "పాస్వర్డ్",
                "login-btn": "లాగిన్",
                "back-button": "వెళ్ళి రా",
                "no-account": "ఖాతా లేదా?",
                "register-link": "ఇక్కడ నమోదు చేయండి"
            }
        };

        function translatePage(language = '<?php echo $_SESSION['language'] ?? 'en'; ?>') {
            if (translations[language]) {
                Object.keys(translations[language]).forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.textContent = translations[language][id];
                    }
                });
            }
        }

        window.onload = () => {
            translatePage();
        };

        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.innerHTML = '&#128065;';
            } else {
                passwordField.type = "password";
                toggleIcon.innerHTML = '&#128065;';
            }
        }
    </script>
</body>
</html>
