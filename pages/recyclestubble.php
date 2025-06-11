<?php
session_start();

// Check if the language is set in the session, if not, default to English
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en'; // Default language
}

// Change language based on user's selection
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, ['en', 'hi', 'te'])) {
        $_SESSION['language'] = $lang;
    }
}

?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['language'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recycle Stubble</title>
    <script>
        function changeLanguage(lang) {
            window.location.href = "?lang=" + lang; // Reload page with selected language
        }
    </script>
    <style>
        /* Your existing CSS styles */
        body {
            background-image: url('https://static.vecteezy.com/system/resources/previews/022/715/810/non_2x/3d-rendering-green-recycle-sign-with-globe-on-background-save-the-world-and-environment-concept-generat-ai-free-photo.jpg');
            background-size: cover;
            background-position: center center;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .recycle-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 600px;
        }
        h2 {
            color: #333;
        }
        .video-links {
            margin-top: 20px;
        }
        .video-links a {
            display: block;
            text-decoration: none;
            background:linear-gradient(135deg, #0f9d58 0%, #0f766e 100%); 
            color: white;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            font-size: 1.2em;
            transition: background 0.3s, transform 0.3s;
        }
        .video-links a:hover {
            background: #0f9d58 0%, #0f766e;
            transform: scale(1.05);
        }

        /* Back button styles */
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
    </style>
</head>
<body>
    <!-- Back Button -->
    <button class="back-button" onclick="window.location.href = 'farmer_dashboard.php';" data-translate="backButton">Back</button>

    <div class="recycle-container">
        <h2 data-translate="title">Learn How to Recycle Stubble</h2>
        <p data-translate="description">Here are some helpful videos that will show you how recycling stubble can benefit you and the environment. Click on the links below to watch the videos:</p>
        <div class="video-links">
            <a href="https://www.youtube.com/watch?v=vT2dhf7xosM" target="_blank" data-translate="video1">How to Recycle Stubble - Video 1</a>
            <a href="https://www.youtube.com/watch?v=zFX1mOsg36w" target="_blank" data-translate="video2">Stubble Recycling Process - Video 2</a>
            <a href="https://www.youtube.com/shorts/a35e_6c96UU" target="_blank" data-translate="video3">Why Recycle Stubble? - Video 3</a>
        </div>
    </div>

    <script>
        // Language Translations
        var translations = {
            'en': {
                'backButton': 'Back',
                'title': 'Learn How to Recycle Stubble',
                'description': 'Here are some helpful videos that will show you how recycling stubble can benefit you and the environment. Click on the links below to watch the videos:',
                'video1': 'How to Recycle Stubble - Video 1',
                'video2': 'Stubble Recycling Process - Video 2',
                'video3': 'Why Recycle Stubble? - Video 3'
            },
            'hi': {
                'backButton': 'वापस',
                'title': 'पराली को पुनः प्रयोग करने के तरीके जानें',
                'description': 'यहाँ कुछ मददगार वीडियो हैं जो आपको दिखाएंगे कि पराली को पुनः प्रयोग करने से आपको और पर्यावरण को कैसे लाभ हो सकता है। वीडियो देखने के लिए नीचे दिए गए लिंक पर क्लिक करें:',
                'video1': 'पराली को पुनः प्रयोग कैसे करें - वीडियो 1',
                'video2': 'पराली पुनः प्रयोग प्रक्रिया - वीडियो 2',
                'video3': 'पराली को क्यों पुनः प्रयोग करें? - वीडियो 3'
            },
            'te': {
                'backButton': 'తిరిగి వెళ్ళు',
                'title': 'పరాలి పునఃప్రయోగం ఎలా చేయాలో తెలుసుకోండి',
                'description': 'ఇక్కడ కొన్ని సహాయక వీడియోలు ఉన్నాయి, అవి పరాలి పునఃప్రయోగం మీకు మరియు పర్యావరణానికి ఎలా లాభకరంగా మారుతుందో చూపిస్తాయి. వీడియోలను చూడటానికి క్రింద ఇచ్చిన లింక్‌లను క్లిక్ చేయండి:',
                'video1': 'పరాలి పునఃప్రయోగం ఎలా చేయాలి - వీడియో 1',
                'video2': 'పరాలి పునఃప్రయోగం ప్రక్రియ - వీడియో 2',
                'video3': 'పరాలి పునఃప్రయోగం ఎందుకు? - వీడియో 3'
            }
        };

        // Apply translations based on the selected language from session
        var currentLang = '<?= $_SESSION['language'] ?>';
        var elements = document.querySelectorAll("[data-translate]");
        elements.forEach(el => {
            var key = el.getAttribute("data-translate");
            el.textContent = translations[currentLang][key];
        });
    </script>
</body>
</html>
