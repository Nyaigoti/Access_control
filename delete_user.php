<?php
require 'connectDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['id'];

    // Validate the ID
    if (!filter_var($userId, FILTER_VALIDATE_INT) === false) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Delete from child tables first
            $stmt = $conn->prepare("DELETE FROM transactions WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM project_members WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            // Now delete the user from the main table
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);

            if ($stmt->execute()) {
                echo "User deleted successfully";
                $conn->commit();  // Commit the transaction
            } else {
                throw new Exception("Error deleting user: " . $conn->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();  // Rollback the transaction in case of any error
            echo "Error: " . $e->getMessage(); // Send the error message as the response
        }
    } else {
        echo "Invalid user ID";
    }
    $conn->close();
} else {
    // Not a POST request
    echo "Error: Only POST method is accepted";
}
?>