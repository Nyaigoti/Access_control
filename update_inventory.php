<?php
include 'connectDB.php';  // Make sure you include your database connection setup

// Check if the required data is posted
if(isset($_POST['component_id']) && isset($_POST['quantity'])) {
    $component_id = $_POST['component_id'];
    $quantity = $_POST['quantity'];

    // Prepare a SQL statement to update data
    $stmt = $conn->prepare("UPDATE inventory SET quantity = ? WHERE component_id = ?");
    $stmt->bind_param("ii", $quantity, $component_id);  // 'ii' means two integers

    // Execute the statement
    if ($stmt->execute()) {
        echo 'success';  // Send 'success' if the update was successful
    } else {
        echo 'Error updating record: ' . $conn->error;  // Error handling
    }

    $stmt->close();  //Close the statement
} else {
    echo 'Missing data';  // Error handling for missing post data
}

$conn->close();  // Close the database connection
?>
