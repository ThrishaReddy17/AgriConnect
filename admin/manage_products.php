<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        td img {
            width: 50px;
            height: auto;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            min-width: 150px;
        }
        .actions a {
            display: inline-block;
            padding: 6px 12px;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
            text-align: center;
            min-width: 60px;
        }
        .actions a.edit-btn {
            background-color: #007bff;
            border: 1px solid #0056b3;
        }
        .actions a.delete-btn {
            background-color: #dc3545;
            border: 1px solid #bd2130;
        }
        .actions a.edit-btn:hover {
            background-color: #0056b3;
        }
        .actions a.delete-btn:hover {
            background-color: #bd2130;
        }
        td {
            vertical-align: middle;
            padding: 12px 8px;
        }
        td img {
            max-width: 50px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .btn-back {
            display: block;
            width: 150px;
            margin: 30px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Products</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($products as $product) : ?>
            <tr>
                <td><?= $product['id']; ?></td>
                <td><?= htmlspecialchars($product['name']); ?></td>
                <td>₹<?= number_format($product['price'], 2); ?></td>
                <td><?= htmlspecialchars($product['description']); ?></td>
                <td><img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="Product Image"></td>
                <td class="actions">
                    <a href="edit_product.php?id=<?= $product['id']; ?>" class="edit-btn">Edit</a>
                    <a href="delete_product.php?id=<?= $product['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>

    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
</div>

</body>
</html>
