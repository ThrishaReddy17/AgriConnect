<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stubble Burning Information Form</title>
    <style>
        body {
            background-image: url('https://static.vecteezy.com/system/resources/previews/019/878/100/non_2x/planting-a-small-plant-on-a-pile-of-soil-with-gardening-tools-on-green-bokeh-background-free-photo.jpg');
            background-size: cover;
            background-position: center center;
            background-attachment: fixed; /* Keeps the background fixed */
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 8px 15px;
            font-size: 1em;
            border-radius: 50%;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
        }
        .back-button:hover {
            background-color: rgba(0, 0, 0, 0.7);
            transform: scale(1.1);
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 550px;
            margin: 50px auto;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 2em;
        }

        label {
            font-size: 1.2em;
            margin-bottom: 5px;
            display: block;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 95%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            border-color: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.5);
        }

        button {
            width: 100%;
            padding: 14px;
            background:linear-gradient(135deg, #0f9d58 0%, #0f766e 100%);
            color: white;
            font-size: 1.1em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background:linear-gradient(135deg, #0f9d58 0%, #0f766e 100%);
            transform: translateY(-7px);
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <a href="farmer_dashboard.php" class="back-button" data-translate="back">⬅</a>
    <div class="form-container">
        <h2 data-translate="stubble_burning_info">Stubble Burning Information Form</h2>
        <form method="POST" action="thank_you.php">
        <label for="farmer_name" data-translate="farmer_name">Farmer Name:</label>
            <input type="text" id="farmer_name" name="farmer_name" required>

            <label for="phone_number" data-translate="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>

            <label for="location" data-translate="location">Location:</label>
            <input type="text" id="location" name="location" required>

            <label for="acres_of_land" data-translate="acres_of_land">Acres of Land:</label>
            <input type="number" id="acres_of_land" name="acres_of_land" step="0.01" required>

            <label for="agricultural_land" data-translate="agricultural_land">Agricultural Land:</label>
            <input type="number" id="agricultural_land" name="agricultural_land" step="0.01" required>

            <label for="non_agricultural_land" data-translate="non_agricultural_land">Non-Agricultural Land:</label>
            <input type="number" id="non_agricultural_land" name="non_agricultural_land" step="0.01" required>

            <label for="estimation_of_stubble" data-translate="estimation_of_stubble">Estimated Stubble Collection Date:</label>
            <input type="date" id="estimation_of_stubble" name="estimation_of_stubble" required>

            <label for="stubble_quantity" data-translate="stubble_quantity">Stubble Quantity (in tons):</label>
            <input type="number" id="stubble_quantity" name="stubble_quantity" step="0.01" required>

            <label for="stubble_recycle" data-translate="stubble_recycle">Do you recycle the stubble?</label>
            <select id="stubble_recycle" name="stubble_recycle" required>
                <option value="Yes" data-translate="yes">Yes</option>
                <option value="No" data-translate="no">No</option>
            </select>

            <button type="submit" data-translate="submit">Submit</button>
        </form>
    </div>
    <script>
         const translations = {
            en: {
                stubble_burning_info: "Stubble Burning Information Form",
                farmer_name: "Farmer Name",
                phone_number: "Phone Number",
                location: "Location",
                acres_of_land: "Acres of Land",
                agricultural_land: "Agricultural Land",
                non_agricultural_land: "Non-Agricultural Land",
                estimation_of_stubble: "Estimated Stubble Collection Date",
                stubble_quantity: "Stubble Quantity (in tons)",
                stubble_recycle: "Do you recycle the stubble?",
                yes: "Yes",
                no: "No",
                submit: "Submit"
            },
            te: {
                stubble_burning_info: "గడ్డి దహన సమాచార ఫారం",
                farmer_name: "రైతు పేరు:",
                phone_number: "ఫోన్ నంబర్:",
                location: "స్థానం:",
                acres_of_land: "ఎకరాల భూమి:",
                agricultural_land: "వ్యవసాయ భూమి:",
                non_agricultural_land: "వ్యవసాయేతర భూమి:",
                estimation_of_stubble: "అంచనా గడ్డి సేకరణ తేదీ:",
                stubble_quantity: "గడ్డి పరిమాణం:(టన్నులలో)",
                stubble_recycle: "మీరు గడ్డిని పునర్వినియోగం చేస్తారా?",
                yes: "అవును",
                no: "కాదు",
                submit: "సమర్పించండి"
            },
            hi: {
                stubble_burning_info: "तिनके जलाने की जानकारी फॉर्म",
                farmer_name: "किसान का नाम:",
                phone_number: "फोन नंबर:",
                location: "स्थान:",
                acres_of_land: "जमीन के एकड़:",
                agricultural_land: "कृषि भूमि:",
                non_agricultural_land: "गैर कृषि भूमि:",
                estimation_of_stubble: "अंदाजन तिनके संग्रह तिथि:",
                stubble_quantity: "तिनके की मात्रा:(टन में)",
                stubble_recycle: "क्या आप तिनके का पुनर्चक्रण करते हैं?",
                yes: "हां",
                no: "नहीं",
                submit: "सबमिट करें"
            }
        };

        // Load language from localStorage or default to 'en'
        let language = localStorage.getItem('language') || 'en';

        // Function to apply translations
        function applyTranslations(language) {
            const elementsToTranslate = document.querySelectorAll('[data-translate]');
            elementsToTranslate.forEach(element => {
                const translationKey = element.getAttribute('data-translate');
                if (translations[language] && translations[language][translationKey]) {
                    element.innerHTML = translations[language][translationKey];
                }
            });
        }

        // Apply the selected language
        applyTranslations(language);

        // Optional: Save the language preference in localStorage when the user selects a language (e.g., via a button or dropdown)
        function changeLanguage(lang) {
            localStorage.setItem('language', lang);
            applyTranslations(lang);
        }
    </script>
</body>
</html>

