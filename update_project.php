<?php
include 'connectDB.php';

if (isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];
    $project_status = $_POST['status'];
    $project_duration = $_POST['duration'];

    $sql = "UPDATE projects SET status = ?, duration = ? WHERE project_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $project_status, $project_duration, $project_id);
    if ($stmt->execute()) {
        echo "Project updated successfully!";
    } else {
        echo "Error updating project: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
} else {
    echo "No project ID provided!";
}
?>
