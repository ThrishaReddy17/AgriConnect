<?php
session_start();

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'farmer') {
    header("Location: login.php");
    exit();
}

$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$user_id = $_SESSION['user_id'];
include('../includes/db.php');

// Fetch profile information
$stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the profile exists, if not redirect to profile form
if (!$profile) {
    header("Location: profile_form.php");
    exit();
}

// Check for POST request to handle product request submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $farmer_name = $profile['full_name'];
    $phone_number = $profile['phone_number'];
    $product_name = trim($_POST['product_name']);
    $price_per_kg = floatval($_POST['price_per_kg']);
    $quantity = floatval($_POST['quantity']);

    // Validate input
    if ($price_per_kg <= 0 || $quantity <= 0) {
        echo "<script>alert('Price and quantity must be greater than 0!');</script>";
    } else {
        try {
            // Insert the product request into the database
            $stmt = $conn->prepare("INSERT INTO product_requests (farmer_name, phone_number, product_name, price_per_kg, quantity, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$farmer_name, $phone_number, $product_name, $price_per_kg, $quantity]);
            
            // Create notification for admin
            $message = "New product request from $farmer_name: $product_name ($quantity kg at ₹$price_per_kg/kg)";
            $stmt = $conn->prepare("INSERT INTO notifications (message, status, order_id) VALUES (?, 'unread', 0)");
            $stmt->execute([$message]);
            
            // Show success message and redirect
            echo "<script>
                    alert('Product request submitted successfully! Admin will review your request.');
                    window.location.href = 'farmer_dashboard.php';
                  </script>";
        } catch (PDOException $e) {
            echo "<script>alert('Error submitting request. Please try again.');</script>";
            error_log("Error in products_add.php: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Request</title>
    <script>
        // Language translation dictionary
        const translations = {
            'en': {
                'title': 'Request Product Addition',
                'farmer_name': 'Farmer Name',
                'phone_number': 'Phone Number',
                'product_name': 'Product Name',
                'price_per_kg': 'Price per Kg',
                'quantity': 'Quantity (Kg)',
                'submit': 'Submit Request',
                'back': 'Back'
            },
            'hi': {
                'title': 'उत्पाद जोड़ने का अनुरोध',
                'farmer_name': 'किसान का नाम',
                'phone_number': 'फ़ोन नंबर',
                'product_name': 'उत्पाद का नाम',
                'price_per_kg': 'किलोग्राम के लिए कीमत',
                'quantity': 'मात्रा (किलोग्राम)',
                'submit': 'अनुरोध सबमिट करें',
                'back': 'वापस'
            },
            'te': {
                'title': 'ఉత్పత్తి జోడింపు అభ్యర్థించండి',
                'farmer_name': 'రైతు పేరు',
                'phone_number': 'ఫోన్ నంబర్',
                'product_name': 'ఉత్పత్తి పేరు',
                'price_per_kg': 'కిలోకు ధర',
                'quantity': 'పరిమాణం (కిలోలు)',
                'submit': 'అభ్యర్థన సమర్పించండి',
                'back': 'తిరిగి వెళ్ళు'
            }
        };

        // Function to change language based on user's preference
        function changeLanguage(lang) {
            localStorage.setItem('language', lang);
            document.querySelectorAll('[data-translate]').forEach(el => {
                const key = el.getAttribute('data-translate');
                el.textContent = translations[lang][key];
            });
            document.querySelectorAll('[data-translate-placeholder]').forEach(el => {
                const key = el.getAttribute('data-translate-placeholder');
                el.setAttribute('placeholder', translations[lang][key]);
            });
        }

        // Initialize language on page load
        window.onload = () => {
            const lang = '<?php echo $language; ?>';
            changeLanguage(lang);
        };
    </script>
    <style>
        /* Basic page styling */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background:rgb(146, 153, 160);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .back-button:hover {
            background:rgb(135, 138, 141);
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: 0.3s ease-in-out;
        }
        .container:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: #28a745;
            color: white;
            font-size: 1.2em;
            cursor: pointer;
            border: none;
            transition: 0.3s;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <!-- Back Button -->
    <a href="farmer_dashboard.php" class="back-button" data-translate="back">Back</a>

    <div class="container">
        <h2 data-translate="title">Request Product Addition</h2>

        <!-- Product Request Form -->
        <form method="POST">
            <input type="text" value="<?php echo htmlspecialchars($profile['full_name']); ?>" disabled data-translate="farmer_name">
            <input type="text" value="<?php echo htmlspecialchars($profile['phone_number']); ?>" disabled data-translate="phone_number">
            <input type="text" name="product_name" placeholder="Enter product name" required data-translate="product_name" data-translate-placeholder="product_name">
            <input type="number" name="price_per_kg" placeholder="Enter price per kg" required min="0.01" step="0.01" data-translate="price_per_kg" data-translate-placeholder="price_per_kg">
            <input type="number" name="quantity" placeholder="Enter quantity in kgs" required min="0.01" step="0.01" data-translate="quantity" data-translate-placeholder="quantity">
            <button type="submit" data-translate="submit">Submit Request</button>
        </form>
    </div>
</body>
</html>
