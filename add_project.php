<?php
session_start();

if (!isset($_SESSION['Admin-name'])) {
    header("Location: login.php");
    exit;
}

require_once 'connectDB.php';

$message = ''; // Variable to store messages for the user

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $projectName = $_POST['project_name'] ?? '';
    $projectMembers = $_POST['project_name'] ?? '';
    $startDate = $_POST['start_date'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $status = $_POST['status'] ?? '';

    // Assuming $_POST['user_id'] is an array of user IDs from your form submission
    if (isset($_POST['project_name'], $_POST['start_date'], $_POST['duration'], $_POST['status'], $_POST['user_id']) && is_array($_POST['user_id'])) {
    // Insert into projects table
    $sql = "INSERT INTO projects (project_name, start_date, duration, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $_POST['project_name'], $_POST['start_date'], $_POST['duration'], $_POST['status']);
    $stmt->execute();
    $project_id = $stmt->insert_id;

    // Insert into project_members table
    $sql_member = "INSERT INTO project_members (project_id, user_id) VALUES (?, ?)";
    $stmt_member = $conn->prepare($sql_member);

    foreach ($_POST['user_id'] as $user_id) {
        $stmt_member->bind_param("ii", $project_id, $user_id);
        $stmt_member->execute();
    }

    echo "Project and members added successfully!";
    // Additional error handling can be added
    $stmt->close();
    $stmt_member->close();
}

    $conn->close();
}

echo htmlspecialchars($message);
?>