<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid product ID!";
    header("Location: manage_products.php");
    exit();
}

$product_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['error'] = "Product not found!";
    header("Location: manage_products.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $quantity = trim($_POST['quantity']);
    $image = $product['image']; 
    if (empty($name) || empty($price) || empty($description) || empty($quantity) || !is_numeric($quantity) || $quantity < 0) {
        $_SESSION['error'] = "All fields are required, and quantity must be a non-negative number!";
        header("Location: edit_product.php?id=" . $product_id);
        exit();
    }
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/";
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "png", "jpeg", "gif"];

        if (in_array($imageFileType, $allowed_types) && move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $image_name;
        } else {
            $_SESSION['error'] = "Error uploading image. Allowed formats: JPG, JPEG, PNG, GIF.";
            header("Location: edit_product.php?id=" . $product_id);
            exit();
        }
    }
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, quantity = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $price, $description, $quantity, $image, $product_id]);

    $_SESSION['message'] = "Product updated successfully!";
    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        img {
            display: block;
            margin: 10px 0;
            width: 100px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($product['price']); ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($product['description']); ?></textarea>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" min="0" value="<?= htmlspecialchars($product['quantity']); ?>" required>

        <label for="image">Product Image:</label>
        <img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="Current Product Image">
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">Update Product</button>
    </form>
    <a href="manage_products.php" class="btn-back">Back to Products</a>
</div>

</body>
</html>
