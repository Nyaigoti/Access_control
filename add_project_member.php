<?php
include 'connectDB.php';

// Check if user_id and project_id are provided
if (isset($_POST['user_id']) && isset($_POST['project_id'])) {
    $user_id = $_POST['user_id'];
    $project_id = $_POST['project_id'];

    // Check if the user is already a member of the project
    $sql = "SELECT * FROM project_members WHERE user_id = $user_id AND project_id = $project_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "User is already a member of this project.";
    } else {
        // Insert the new member into the project_members table
        $sql = "INSERT INTO project_members (user_id, project_id) VALUES ($user_id, $project_id)";

        if ($conn->query($sql) === TRUE) {
            echo "Member added successfully.";
        } else {
            echo "Error adding member: " . $conn->error;
        }
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>