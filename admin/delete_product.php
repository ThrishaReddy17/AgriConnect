<?php
include '../includes/db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if 'id' is set in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // Retrieve product image filename to delete from server
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Delete product from database
        $delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $delete_stmt->execute([$product_id]);

        // Delete product image if exists (avoid deleting default images)
        if (!empty($product['image']) && file_exists("../images/" . $product['image'])) {
            unlink("../images/" . $product['image']);
        }

        // Redirect with success message
        $_SESSION['message'] = "Product deleted successfully!";
        header("Location: manage_products.php");
        exit();
    } else {
        $_SESSION['error'] = "Product not found!";
        header("Location: manage_products.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid product ID!";
    header("Location: manage_products.php");
    exit();
}
?>
