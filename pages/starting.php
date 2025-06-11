<?php
session_start();

// Check if a language is selected, if yes, store it in session
if (isset($_POST['language'])) {
    $_SESSION['language'] = $_POST['language'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to AgriConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: #f4f6f7;
        }

        .navbar {
            background-color: #2c6e49;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .navbar .logo {
            font-size: 2em;
            color: #ffffff;
            font-weight: bold;
        }

        .navbar .nav-links {
            display: flex;
            align-items: center;
        }

        .navbar .nav-links a {
            margin-left: 25px;
            font-size: 1.1em;
            color: #ffffff;
            text-decoration: none;
            padding: 8px 22px;
            border-radius: 5px;
            background-color: #1e5d3e;
            transition: background 0.3s;
        }

        .navbar .nav-links a:hover {
            background-color: #18573d;
        }

        .language-selector {
            margin-left: 15px;
            padding: 7px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #ffffff;
            color: #2c6e49;
        }

        .hero-section {
            height: 100vh;
            background: url('https://static.vecteezy.com/system/resources/previews/019/878/100/non_2x/planting-a-small-plant-on-a-pile-of-soil-with-gardening-tools-on-green-bokeh-background-free-photo.jpg') no-repeat center center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 20px;
            margin-top: 80px;
        }

        .hero-section h1 {
            font-size: 3.5em;
            margin-bottom: 25px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-section p {
            font-size: 1.4em;
            max-width: 700px;
            margin: 0 auto;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .scroll-down {
            position: absolute;
            bottom: 50px;
            font-size: 1.2em;
            background: #28a745;
            color: white;
            padding: 15px 35px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .scroll-down:hover {
            background: #218838;
        }

        .info-section {
            background: #ffffff;
            padding: 80px 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 60px;
        }

        .info-section h2 {
            font-size: 2.8em;
            margin-bottom: 25px;
            color: #2c6e49;
            text-transform: uppercase;
            font-weight: bold;
        }

        .info-section p {
            font-size: 1.2em;
            max-width: 800px;
            margin: 0 auto;
            color: #666;
            line-height: 1.6;
        }

        .get-started {
            display: inline-block;
            margin-top: 35px;
            background: #28a745;
            color: white;
            padding: 18px 35px;
            font-size: 1.4em;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo" id="logo">🌱AgriConnect</div>
        <div class="nav-links">
            <a href="login.php" id="login-button">Login</a>
            <a href="register.php" id="register-button">Register</a>
            <form method="POST" action="starting.php">
                <select class="language-selector" name="language" onchange="this.form.submit()">
                    <option value="en" <?php echo ($_SESSION['language'] == 'en' ? 'selected' : ''); ?>>English</option>
                    <option value="hi" <?php echo ($_SESSION['language'] == 'hi' ? 'selected' : ''); ?>>हिन्दी (Hindi)</option>
                    <option value="te" <?php echo ($_SESSION['language'] == 'te' ? 'selected' : ''); ?>>తెలుగు (Telugu)</option>
                </select>
            </form>
        </div>
    </div>

    <div class="hero-section">
        <h1 id="title">Welcome to AgriConnect</h1>
        <p id="description">Empowering farmers with technology and resources to maximize their profitability. Join us in revolutionizing agriculture for a better future.</p>
        <a href="#info-section" class="scroll-down" id="learn-more">Learn More</a>
    </div>

    <!-- Information Section -->
    <div class="info-section" id="info-section">
        <h2 id="info-title">AgriConnect Info</h2>
        <p id="info-description">Farmers are the backbone of our society, providing essential food and resources for everyone. Our platform is designed to help farmers increase their profits by providing valuable insights, connecting them to better markets, and offering innovative farming solutions.</p>
        <a href="register.php" class="get-started" id="get-started">Get Started</a>
    </div>

    <script>
        const translations = {
            en: {
                title: "Welcome to AgriConnect", 
                description: "Empowering farmers with technology and resources to maximize their profitability. Join us in revolutionizing agriculture for a better future.",
                "learn-more": "Learn More",
                "login": "Login",
                "register": "Register",
                "info-title": "Why AgriConnect?",
                "info-description": "Farmers are the backbone of our society, providing essential food and resources for everyone. Our platform is designed to help farmers increase their profits by providing valuable insights, connecting them to better markets, and offering innovative farming solutions.",
                "get-started": "Get Started"
            },
            hi: {
                title: "एग्रीकनेक्ट में आपका स्वागत है",
                description: "किसानों को प्रौद्योगिकी और संसाधनों के साथ सशक्त बनाना ताकि वे अपनी लाभप्रदता को अधिकतम कर सकें। कृषि में क्रांति लाने के लिए हमारे साथ जुड़ें।",
                "learn-more": "और अधिक जानें",
                "login": "लॉगिन",
                "register": "रजिस्टर",
                "info-title": "एग्रीकनेक्ट जानकारी",
                "info-description": "किसान हमारे समाज की रीढ़ हैं, जो सभी के लिए आवश्यक खाद्य और संसाधन प्रदान करते हैं। हमारा मंच किसानों को उनके लाभ बढ़ाने में मदद करने के लिए डिज़ाइन किया गया है, जो मूल्यवान अंतर्दृष्टि प्रदान करता है, उन्हें बेहतर बाजारों से जोड़ता है, और नवाचारात्मक कृषि समाधान प्रदान करता है।",
                "get-started": "शुरू करें"
            },
            te: {
                title: "ఎగ్రికనెక్ట్ కు స్వాగతం",
                description: "రైతులను సాంకేతికత మరియు వనరులతో శక్తివంతం చేయడం, వారు తమ లాభాలను పెంచుకునేలా చేయడం. వ్యవసాయ రంగంలో విప్లవాత్మక మార్పుల కోసం మాతో చేరండి.",
                "learn-more": "మరింత తెలుసుకోండి",
                "login": "లాగిన్",
                "register": "రిజిస్టర్",
                "info-title": "ఎగ్రికనెక్ట్ సమాచారం",
                "info-description": "రైతులు మన సమాజం యొక్క వెన్నెముక, ప్రతి ఒక్కరికీ అవసరమైన ఆహారం మరియు వనరులు అందించడంలో సహాయపడుతున్నారు. మా ప్లాట్‌ఫారం రైతులకు మౌలిక సమాచారాన్ని అందించడం, మెరుగైన మార్కెట్లకు వారిని అనుసంధానించడం, మరియు నూతన వ్యవసాయ పరిష్కారాలను అందించడం ద్వారా వారి లాభాలను పెంచడంలో సహాయపడుతుంది.",
                "get-started": "ప్రారంభించండి"
            }
        };

        function translatePage(language = '<?php echo $_SESSION['language'] ?? 'en'; ?>') {
            const textElements = document.querySelectorAll('[id]');

            textElements.forEach(element => {
                const key = element.id;
                if (translations[language][key]) {
                    element.innerText = translations[language][key];
                }
            });
        }

        window.onload = () => {
            translatePage();
        }
    </script>
</body>
</html>
