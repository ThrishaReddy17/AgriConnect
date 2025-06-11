<?php
// Include Twilio SDK
require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;

// Twilio credentials - These should be set in your environment variables
// For development, you can set them here temporarily
$sid = getenv('TWILIO_ACCOUNT_SID') ?: 'YOUR_ACCOUNT_SID';  // Replace with your actual Twilio Account SID
$auth_token = getenv('TWILIO_AUTH_TOKEN') ?: 'YOUR_AUTH_TOKEN';  // Replace with your actual Twilio Auth Token
$twilio_number = getenv('TWILIO_PHONE_NUMBER') ?: 'YOUR_TWILIO_NUMBER';  // Replace with your actual Twilio phone number

// Get the farmer's phone number from form submission
$farmer_phone_number = $_POST['phone_number'] ?? '';

// Format the phone number to E.164 format if not already formatted
if (!empty($farmer_phone_number)) {
    // Remove any non-numeric characters
    $farmer_phone_number = preg_replace('/[^0-9]/', '', $farmer_phone_number);
    
    // Add country code if not present (assuming Indian numbers)
    if (strlen($farmer_phone_number) === 10) {
        $farmer_phone_number = '+91' . $farmer_phone_number;
    } elseif (strlen($farmer_phone_number) === 12 && substr($farmer_phone_number, 0, 2) === '91') {
        $farmer_phone_number = '+' . $farmer_phone_number;
    }
}

// Initialize SMS status
$sms_status = '';

// Only attempt to send SMS if we have valid Twilio credentials
if ($sid !== 'YOUR_ACCOUNT_SID' && $auth_token !== 'YOUR_AUTH_TOKEN' && $twilio_number !== 'YOUR_TWILIO_NUMBER') {
    try {
        // Create Twilio client
        $client = new Client($sid, $auth_token);

        // Send SMS to farmer if phone number is valid
        if (!empty($farmer_phone_number)) {
            // Sending the message via Twilio
            $message = $client->messages->create(
                $farmer_phone_number,  // To number (Farmer's phone number)
                [
                    'from' => $twilio_number,  // Your Twilio phone number
                    'body' => 'We have received your stubble burning information. We will process your request soon. Thank you!'
                ]
            );
            $sms_status = "Notification sent successfully!";
        } else {
            $sms_status = "No phone number provided for SMS notification.";
        }
    } catch (Exception $e) {
        $sms_status = "SMS notification could not be sent: " . $e->getMessage();
        // Log the error but don't stop the process
        error_log("SMS Error: " . $e->getMessage());
    }
} else {
    $sms_status = "SMS notifications are not configured.";
    error_log("SMS credentials not configured");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .thank-you-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        h1 {
            color: #28a745;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .status {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f8f9fa;
            color: #666;
        }
        .status.error {
            background-color: #fff3f3;
            color: #dc3545;
        }
        .status.success {
            background-color: #f0fff4;
            color: #28a745;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <h1>Thank You!</h1>
        <p>Your stubble burning information has been submitted successfully.</p>
        <p>We will process your request and get back to you soon.</p>
        
        <?php if (isset($sms_status)): ?>
            <div class="status <?php echo strpos($sms_status, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($sms_status); ?>
            </div>
        <?php endif; ?>
        
        <a href="farmer_dashboard.php" class="button">Return to Dashboard</a>
    </div>
</body>
</html>
