<?php
include 'connectDB.php';

// Check if user_id and project_id are provided
if (isset($_POST['user_id']) && isset($_POST['project_id'])) {
    $user_id = $_POST['user_id'];
    $project_id = $_POST['project_id'];

    // Delete the member from the project_members table
    $sql = "DELETE FROM project_members WHERE user_id = ? AND project_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $project_id);

    if ($stmt->execute()) {
        echo "Member deleted successfully.";
    } else {
        echo "Error deleting member: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}

$stmt->close();
$conn->close();
?>