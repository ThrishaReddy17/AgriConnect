<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover More - AgriConnect</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(51, 51, 51, 0.7);
            padding: 10px 20px;
            color: white;
        }
        .navbar h1 {
            margin: 0;
            font-size: 1.8em;
        }
        .logout-btn {
            background-color: rgb(167, 64, 74);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        .back-btn {
            display: block;
            margin: 15px;
            padding: 10px 20px;
            background-color: #059669;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            width: fit-content;
        }
        .content {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }
        .grid-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .grid-item {
            display: flex;
            background: white;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            align-items: center;
            padding: 15px;
        }
        .grid-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
        }
        .grid-item div {
            flex: 1;
        }
        h2 {
            color: #333;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>AgriConnect</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    <a href="farmer_dashboard.php" class="back-btn">&#8592; Back</a>
    <div class="content">
        <div class="grid-container">
            <div class="grid-item">
                <img src="../images/stubble.jpg" alt="Stubble Burning">
                <div>
                    <h2>Stubble Burning</h2>
                    <p>Stubble burning is a significant environmental concern, contributing to air pollution and soil degradation. Sustainable alternatives like mulching and bio-decomposition can help farmers manage stubble effectively.</p>
                </div>
            </div>
            <div class="grid-item">
                <img src="../images/recycle.jpg" alt="Recycle Stubble">
                <div>
                    <h2>Recycle Stubble</h2>
                    <p>Recycling stubble is an eco-friendly way to convert crop residue into useful products like biofuel, compost, and animal fodder. This helps reduce pollution and promotes sustainable farming.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
