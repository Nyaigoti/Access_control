<?php
include 'connectDB.php';
$project_id = $_GET['project_id'];

// Fetch project members
$sql = "SELECT users.username, users.id as user_id FROM project_members JOIN users ON users.id = project_members.user_id WHERE project_members.project_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($member = $result->fetch_assoc()) {
        $username = htmlspecialchars($member['username']);
        $user_id = $member['user_id'];
        echo "<li>{$username} <button onclick='removeMember({$user_id}, {$project_id})'>Remove</button></li>";
    }
} else {
    echo "No members found.";
}
$conn->close();
?>



    

