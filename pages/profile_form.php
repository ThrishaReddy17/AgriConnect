<?php
session_start();

// Check if language is set in session, otherwise default to English
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en'; // Default to English
}

// Include your database connection (db.php)
include('../includes/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's role
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php");
    exit();
}

$user_role = $user['role']; // Assuming 'role' is 'farmer', 'user', or 'organization'

// Check if the profile already exists
$stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$existingProfile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingProfile) {
    // If profile already exists, redirect to the appropriate dashboard (or cart.php for a user) based on his role.
    if (strtolower($user_role) === 'farmer') {
        header("Location: farmer_dashboard.php");
    } elseif (strtolower($user_role) === 'organization') {
        header("Location: organization.php");
    } elseif (strtolower($user_role) === 'user') {
        header("Location: cart.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Handle POST submission (profile form submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Collect form data (sanitize as needed)
    $full_name = trim($_POST['full_name']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $organization_name = (isset($_POST['organization_name']) ? trim($_POST['organization_name']) : null);
    $agricultural_land = (isset($_POST['agricultural_land']) ? (int) $_POST['agricultural_land'] : null);
    $farm_size = (isset($_POST['farm_size']) ? trim($_POST['farm_size']) : null);

    // Insert (or update) the profile record (using a prepared statement)
    $sql = "INSERT INTO profiles (user_id, full_name, phone_number, address, organization_name, agricultural_land, farm_size) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $full_name, $phone_number, $address, $organization_name, $agricultural_land, $farm_size]);

    // Redirect the user to his respective dashboard (or cart.php for a user) based on his role.
    if (strtolower($user_role) === 'farmer') {
        header("Location: farmer_dashboard.php");
    } elseif (strtolower($user_role) === 'organization') {
        header("Location: organization.php");
    } elseif (strtolower($user_role) === 'user') {
        header("Location: cart.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Translations for each language
$translations = [
    'en' => [
        'title' => 'Create Your Profile',
        'fullname' => 'Full Name:',
        'phone' => 'Phone Number:',
        'address' => 'Address:',
        'agricultural_land' => 'Agricultural Land (in acres):',
        'farm_size' => 'Farm Size:',
        'organization_name' => 'Organization Name:',
        'profile_image' => 'Profile Image:',
        'submit_profile' => 'Submit Profile'
    ],
    'hi' => [
        'title' => 'अपनी प्रोफ़ाइल बनाएं',
        'fullname' => 'पूरा नाम:',
        'phone' => 'फोन नंबर:',
        'address' => 'पता:',
        'agricultural_land' => 'कृषि भूमि (एकड़ में):',
        'farm_size' => 'खेत का आकार:',
        'organization_name' => 'संगठन का नाम:',
        'profile_image' => 'प्रोफाइल छवि:',
        'submit_profile' => 'प्रोफ़ाइल सबमिट करें'
    ],
    'te' => [
        'title' => 'ఆపనిది ప్రొఫైల్ చేయండి',
        'fullname' => 'పూర్తి పేరు:',
        'phone' => 'ఫోన్ నెంబర్:',
        'address' => 'చిరునామా:',
        'agricultural_land' => 'వ్యవసాయ భూమి (ఏకరాల్లో):',
        'farm_size' => 'ఫారమ్ పరిమాణం:',
        'organization_name' => 'సంస్థ పేరు:',
        'profile_image' => 'ప్రొఫైల్ చిత్రం:',
        'submit_profile' => 'ప్రొఫైల్ సమర్పించండి'
    ]
];

// Set the language to the session language
$lang = $_SESSION['language'];

?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations[$lang]['title']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            font-size: 14px;
            color: #555;
            display: block;
            margin: 10px 0 5px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input[type="file"] {
            padding: 5px;
        }
        button {
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1><?php echo $translations[$lang]['title']; ?></h1>

        <form method="POST" enctype="multipart/form-data">
            <label for="full_name"><?php echo $translations[$lang]['fullname']; ?></label>
            <input type="text" name="full_name" id="full_name" required>

            <label for="phone_number"><?php echo $translations[$lang]['phone']; ?></label>
            <input type="text" name="phone_number" id="phone_number" required>

            <label for="address"><?php echo $translations[$lang]['address']; ?></label>
            <textarea name="address" id="address" rows="3" required></textarea>

            <?php if ($user_role == 'farmer'): ?>
                <label for="agricultural_land"><?php echo $translations[$lang]['agricultural_land']; ?></label>
                <input type="number" name="agricultural_land" id="agricultural_land" required>

                <label for="farm_size"><?php echo $translations[$lang]['farm_size']; ?></label>
                <input type="text" name="farm_size" id="farm_size" required>
            <?php endif; ?>

            <?php if ($user_role == 'organization'): ?>
                <label for="organization_name"><?php echo $translations[$lang]['organization_name']; ?></label>
                <input type="text" name="organization_name" id="organization_name" required>
            <?php endif; ?>

            <label for="profile_image"><?php echo $translations[$lang]['profile_image']; ?></label>
            <input type="file" name="profile_image" id="profile_image">

            <button type="submit" name="submit"><?php echo $translations[$lang]['submit_profile']; ?></button>
        </form>
    </div>

</body>
</html>
