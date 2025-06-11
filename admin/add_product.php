<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$name = "";
$price = "";
$description = "";
$image = "";
$request_id = null; 
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT product_name, price_per_kg, quantity FROM product_requests WHERE id = ? AND status = 'pending'");
    $stmt->execute([$request_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $name = $product['product_name'];
        $price = $product['price_per_kg'];
        $description = "Quantity: " . $product['quantity'] . " Kg";
    }
}
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    if ($image) {
        move_uploaded_file($_FILES['image']['tmp_name'], "../images/$image");
    } else {
        $image = "default.jpg";
    }
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $description, $image]);
    if ($request_id) {
        $stmt = $conn->prepare("UPDATE product_requests SET status = 'approved' WHERE id = ?");
        $stmt->execute([$request_id]);
    }
    echo "<script>alert('Product added successfully!'); window.location.href = 'manage_products.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], input[type="number"], textarea, input[type="file"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            height: 100px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .back-link a {
            color: #4CAF50;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Product Name:</label>
            <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($name); ?>">

            <label for="price">Price per Kg:</label>
            <input type="number" step="0.01" name="price" id="price" required value="<?php echo htmlspecialchars($price); ?>">

            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($description); ?></textarea>

            <label for="image">Image:</label>
            <input type="file" name="image" id="image" <?php echo $request_id ? '' : 'required'; ?>>

            <button type="submit" name="add_product">Add Product</button>
        </form>

        <div class="back-link">
            <a href="manage_products.php">Back to Manage Products</a>
        </div>
    </div>
</body>
</html>
