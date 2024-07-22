<?php
// Start session and include database connection
session_start();
include 'connectDB.php';

// Check if ID is posted and is a valid numeric value
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $component_id = $_POST['id'];
    
    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete from child table first
        $stmt = $conn->prepare("DELETE FROM transactions WHERE component_id = ?");
        $stmt->bind_param("i", $component_id);
        $stmt->execute();
        $stmt->close();

        // Now delete the main inventory component
        $stmt = $conn->prepare("DELETE FROM inventory WHERE component_id = ?");
        $stmt->bind_param("i", $component_id);

        if ($stmt->execute()) {
            echo "success"; // Send a success response
            $conn->commit();  // Commit the transaction
        } else {
            throw new Exception("Error deleting component: " . $conn->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();  // Rollback the transaction in case of any error
        echo $e->getMessage(); // Send the error message as the response
    }

    $conn->close();
} else {
    echo "Invalid component ID.";
}
?>