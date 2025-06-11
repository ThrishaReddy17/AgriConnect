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

// Fetch profile details
$stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// If profile doesn't exist, redirect to profile form
if (!$profile) {
    header("Location: profile_form.php");
    exit();
}

// Define images and their respective details
$gridItems = [
    ["image" => "stubble.jpg", "title" => "Stubble Burning", "desc" => "Learn about the impacts and solutions for stubble burning.", "link" => "stubbleburning.php"],
    ["image" => "recycle.jpg", "title" => "Recycle Stubble", "desc" => "Discover eco-friendly ways to recycle stubble effectively.", "link" => "recyclestubble.php"],
    ["image" => "cart.jpg", "title" => "Add Product Request", "desc" => "Submit requests to add new products for sale.", "link" => "products_add.php"]
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>
    <script>
        function openProfile() {
            document.getElementById('profileSidebar').style.width = '300px';
        }
        function closeProfile() {
            document.getElementById('profileSidebar').style.width = '0';
        }
    </script>
    <style>
        body {
            background: url('../images/untitled.jpg') no-repeat center center/cover;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .navbar {
            width: 100%;
            background-color: rgba(51, 51, 51, 0.7);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .navbar h1 {
            margin: 0;
            font-size: 1.8em;
        }

        .profile-icon {
            font-size: 1.5em;
            cursor: pointer;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 50px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }

        .grid-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            padding: 20px;
            height: 320px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .grid-item:hover {
            transform: scale(1.05);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
        }

        .grid-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease-in-out;
        }

        .grid-item:hover img {
            transform: scale(1.1);
        }

        .grid-item h3 {
            font-size: 1.4em;
            color: #333;
            margin: 15px 0 10px;
        }

        .grid-item p {
            font-size: 1em;
            color: #666;
            padding: 0 15px;
            margin-bottom: 15px;
        }

        .grid-item a {
            display: inline-block;
            background: #0f9d58;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease-in-out;
        }

        .grid-item a:hover {
            background: #0f766e;
        }

        @media (max-width: 1024px) {
            .grid-container {
                grid-template-columns: repeat(2, 1fr);
                max-width: 800px;
            }
        }

        @media (max-width: 600px) {
            .grid-container {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>AGRICONNECT</h1>
    <div style="display: flex; gap: 15px; align-items: center;">
        <a href="farmer_home.php" style="background-color:#4CAF50; color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold;">Home</a>
        <a href="farmer_profile.php" style="background-color:#2196F3; color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold;">Profile</a>
        <a href="logout.php" style="background-color:rgb(167, 64, 74); color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold;">Logout</a>
    </div>
</div>

    <div class="grid-container">
        <?php foreach ($gridItems as $item) { ?>
            <div class="grid-item">
                <img src="../images/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>">
                <h3><?php echo $item['title']; ?></h3>
                <p><?php echo $item['desc']; ?></p>
                <a href="<?php echo $item['link']; ?>">Explore</a>
            </div>
        <?php } ?>
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 1.2em; font-weight: bold; color: rgba(251, 251, 251, 0.88);">
        "Transforming stubble from waste to wealth – sustainable solutions for a greener tomorrow."
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="more.php" style="background-color: #e5e7e9; color: #059669; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; border: 2px solid #059669; transition: 0.3s ease-in-out; font-size: 1em;">
            Discover More
        </a>
    </div>
</body>
</html>
