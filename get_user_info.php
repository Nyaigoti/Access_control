<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
    exit;  // Ensure no further execution happens if not logged in
}

// Connect to the database
require 'connectDB.php';

$userId = $_POST['id'];


// Query the database to get the user's projects
$projectsQuery = "
    SELECT p.project_name
    FROM projects p
    JOIN project_members pm ON p.project_id = pm.project_id
    WHERE pm.user_id = $userId
";
$projectsResult = mysqli_query($conn, $projectsQuery);
$projects = array();
while ($row = mysqli_fetch_assoc($projectsResult)) {
    $projects[] = $row['project_name'];
}

// Query the database to get the user's borrowed components and the quantity based on transaction type
$borrowedComponentsQuery = "
    SELECT i.name, SUM(CASE WHEN t.transaction_type = 'assign' THEN t.quantity ELSE -t.quantity END) AS quantity
    FROM inventory i
    JOIN transactions t ON i.component_id = t.component_id AND t.user_id = $userId
    GROUP BY i.component_id
    HAVING quantity > 0
";
$borrowedComponentsResult = mysqli_query($conn, $borrowedComponentsQuery);
$borrowedComponents = array();
while ($row = mysqli_fetch_assoc($borrowedComponentsResult)) {
    $borrowedComponents[] = $row['name'] . ' (Quantity: ' . $row['quantity'] . ')';
}

// Return the user's projects and borrowed components as a JSON response
$response = array(
    'projects' => $projects,
    'borrowedComponents' => $borrowedComponents
);
echo json_encode($response);
?>