<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Your database username
$password = ""; // Your database password (default is empty for XAMPP)
$dbname = "ecommerce"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$farmer_name = $_POST['farmer_name'];
$phone_number = $_POST['phone_number'];
$location = $_POST['location'];
$acres_of_land = $_POST['acres_of_land'];
$agricultural_land = $_POST['agricultural_land'];
$non_agricultural_land = $_POST['non_agricultural_land'];
$estimation_of_stubble = $_POST['estimation_of_stubble'];
$stubble_quantity = $_POST['stubble_quantity'];
$stubble_recycle = $_POST['stubble_recycle'];

// Insert into database
$sql = "INSERT INTO stubbleburning(farmer_name, phone_number, location, acres_of_land, 
          agricultural_land, non_agricultural_land, estimation_of_stubble, stubble_quantity, stubble_recycle) 
        VALUES ('$farmer_name', '$phone_number', '$location', '$acres_of_land', 
                '$agricultural_land', '$non_agricultural_land', '$estimation_of_stubble', 
                '$stubble_quantity', '$stubble_recycle')";

if ($conn->query($sql) === TRUE) {
    // Get the ID of the inserted stubble burning request
    $stubble_id = $conn->insert_id;
    
    // Create notification for organizations
    $message = "New stubble burning request from $farmer_name: $stubble_quantity tons at $location";
    $notification_sql = "INSERT INTO notifications (message, status, stubble_id) VALUES (?, 'unread', ?)";
    $notification_stmt = $conn->prepare($notification_sql);
    $notification_stmt->bind_param("si", $message, $stubble_id);
    $notification_stmt->execute();
    
    // Create a form to submit the phone number to thank_you_page.php
    echo "<form id='redirectForm' method='POST' action='thank_you_page.php'>";
    echo "<input type='hidden' name='phone_number' value='" . htmlspecialchars($phone_number) . "'>";
    echo "</form>";
    echo "<script>";
    echo "document.getElementById('redirectForm').submit();";
    echo "</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

