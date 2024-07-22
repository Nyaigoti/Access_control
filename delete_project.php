<?php
session_start();
include 'connectDB.php';

if(isset($_POST['project_id']) && is_numeric($_POST['project_id'])) {
    $project_id = $_POST['project_id'];
    
    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete child rows first
        $stmt = $conn->prepare("DELETE FROM project_members WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $stmt->close();

        // Now, delete the parent row from 'projects'
        $stmt = $conn->prepare("DELETE FROM projects WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        
        if ($stmt->execute()) {
            echo "Project and related tasks deleted successfully.";
            $conn->commit();  // Commit the transaction
        } else {
            echo "Error deleting project.";
            $conn->rollback();  // Rollback the transaction in case of failure
        }
        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();  // Rollback the transaction on any error
        echo "An error occurred: " . $e->getMessage();
    }

    $conn->close();
} else {
    echo "Invalid project ID.";
}
?>
