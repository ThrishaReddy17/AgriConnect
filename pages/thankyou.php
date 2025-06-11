<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - AGRITECH</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
            margin: 0;
            text-align: center;
            color: #333;
        }
        .thank-you-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 90%;
            animation: fadeIn 1s ease-in-out;
        }
        h1 {
            color: #28a745;
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }
        .message {
            margin: 15px 0;
            font-size: 1.5em;
        }
        .back-home, .login-link {
            margin-top: 30px;
            text-decoration: none;
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 8px;
            display: inline-block;
            transition: background-color 0.3s;
            margin: 10px;
        }
        .back-home:hover, .login-link:hover {
            background-color: #218838;
        }
        .login-link i {
            margin-right: 8px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

<div class="thank-you-container">
    <h1><i class="fas fa-seedling"></i> Registration Successful!</h1>
    <div class="message">
        <p>✅ <strong>Thank You for Registering!</strong></p>
        <p>🇮🇳 हिंदी: <strong>पंजीकरण के लिए धन्यवाद!</strong></p>
        <p>🇮🇳 తెలుగు: <strong>నమోదు కోసం ధన్యవాదాలు!</strong></p>
    </div>

    <a href="index.php" class="back-home">🔙 Go to Home</a>
    <a href="login.php" class="login-link"><i class="fas fa-sign-in-alt"></i> Login</a>
</div>

</body>
</html>
