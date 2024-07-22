<?php
// Database configuration
$dbHost     = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName     = 'biometricattendance';

// Connect to the database
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check for database connection error
if($db->connect_error){
    die("Connection failed: " . $db->connect_error);
}

// Check if the request is a GET request
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    // Validate and sanitize input
    $finger_device_id = isset($_GET['fingerId']) ? mysqli_real_escape_string($db, $_GET['fingerId']) : "";
    $clock_mode = isset($_GET['direction']) ? (int)$_GET['direction'] : -1;
    
    // Validate inputs
    if(!empty($finger_device_id) && $clock_mode != -1){
        // Insert data into the database
        $query = "INSERT INTO logs (finger_device_id, clock_mode) VALUES ('$finger_device_id', '$clock_mode')";
        if($db->query($query)){
            // If insert is successful
            $response = array('status' => 'success', 'message' => 'Data logged successfully.');
        } else {
            // If insert fails
            $response = array('status' => 'error', 'message' => 'Failed to log data.');
        }
    } else {
        // If validation fails
        $response = array('status' => 'error', 'message' => 'Invalid input.');
    }
} else {
    // If not a GET request
    $response = array('status' => 'error', 'message' => 'Invalid request method.');
}

// Close database connection
$db->close();

// Return response in JSON format
header('Content-Type: application/json');
echo json_encode($response);
?>
