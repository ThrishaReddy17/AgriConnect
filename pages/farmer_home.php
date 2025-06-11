<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriConnect - Smart Farming Assistant</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            background: url('https://images.nationalgeographic.org/image/upload/t_edhub_resource_key_image/v1638892233/EducationHub/photos/crops-growing-in-thailand.jpg') 
                        no-repeat center center/cover;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar h1 {
            font-size: 30px;
            color: #333;
        }
        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: 0.3s;
            padding: 10px 0;
            font-size: 1.2em; /* Increased font size for better readability */
        }
        .nav-links a:hover {
            color: #ff5f5f;
        }
        .explore-now {
            background: #ff5f5f;
            color: white;
            padding: 20px 40px; /* Added extra space inside without increasing button size */
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2em;
            transition: 0.3s;
        }
        .explore-now:hover {
            background: #e64a4a;
        }
        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            background: rgba(0, 0, 0, 0.4); /* Slight overlay for readability */
            width: 100%;
        }
        .hero h1 {
            font-size: 3em;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        }
        .hero p {
            font-size: 1.2em;
            margin: 10px 0;
        }
        .explore-btn {
            background: #e64a4a;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .explore-btn:hover {
            background: #0d8ae0;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <h1>AgriConnect</h1>
        <div class="nav-links">
            <a href="farmer_dashboard.php">Features</a>
            <a href="more.php">About</a>
            <a href="farmer_profile.php">Profile</a>
            <a href="farmer_dashboard.php" class="explore-now">Explore Now</a>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <h1>YOUR SMART FARMING ASSISTANT</h1>
        <p>Smart Crops, Smart Choices With AgriConnect!</p>
        <a href="farmer_dashboard.php" class="explore-btn">Explore Features</a>
    </div>

</body>
</html>