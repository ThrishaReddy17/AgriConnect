<?php
include('../includes/db.php');
session_start();

if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    if (empty($email) || empty($password) || empty($role)) {
        $error_message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $error_message = "Email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$email, $hashedPassword, $role]);

            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['role'] = $role;
            $_SESSION['email'] = $email;
            
            // Store a flag in session to indicate profile completion is needed
            $_SESSION['needs_profile'] = true;
            
            echo "<script>alert('Registration successful! Please complete your profile.'); window.location.href='profile_form.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .register-container {
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
        .register-container:hover {
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
        input[type="password"],
        select {
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
        input[type="password"]:focus,
        select:focus {
            border-color: #28a745;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            font-size: 1.2em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            background:linear-gradient(135deg, #0f9d58 0%, #0f766e 100%);
        }
        button:hover {
            background-color: #218838;
           transform:translateY(-5px);
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
            padding-right: 40px; /* Add space for the icon */
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
    </style>
</head>
<body>

    <!-- Back Button -->
    <a href="starting.php" class="back-button" id="back-button">Back</a>

    <div class="register-container">
        <h2 id="register-title">Register</h2>
        <form method="POST">
            <label id="email-label">Email:</label>
            <input type="email" name="email" required>
            <label id="password-label">Password:</label>
            <div class="password-container">
                <input type="password" name="password" id="password" required>
                <i class="eye-icon" id="togglePassword" onclick="togglePassword()">&#128065;</i>
            </div>
            <label id="role-label">User Type:</label>
            <select name="role" required>
                <option value="User">User</option>
                <option value="Farmer">Farmer</option>
                <option value="Organization">Organization</option>
            </select>
            <button type="submit" name="register" id="register-btn">Register</button>
        </form>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </div>

    <script>
        const translations = {
            en: {
                "register-title": "Register",
                "email-label": "Email",
                "password-label": "Password",
                "role-label": "User Type",
                "register-btn": "Register",
                "back-button": "Back"
            },
            hi: {
                "register-title": "पंजीकरण करें",
                "email-label": "ईमेल",
                "password-label": "पासवर्ड",
                "role-label": "उपयोगकर्ता प्रकार",
                "register-btn": "पंजीकरण करें",
                "back-button": "वापस"
            },
            te: {
                "register-title": "నమోదు",
                "email-label": "ఇమెయిల్",
                "password-label": "పాస్వర్డ్",
                "role-label": "వాడుకరి రకం",
                "register-btn": "నమోదు చేయండి",
                "back-button": "వెళ్ళి రా"
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
