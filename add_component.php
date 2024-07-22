<?php
include 'connectDB.php';

$name = $_POST['name'];
$quantity = $_POST['quantity'];

$sql = "INSERT INTO inventory (name, quantity) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $name, $quantity);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "success";
    header("location: inventory.php?success=registered");
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
