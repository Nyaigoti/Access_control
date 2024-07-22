<?php
include 'connectDB.php';

$conn->autocommit(FALSE); // Start transaction

$component_id = isset($_POST['component_id']) ? (int)$_POST['component_id'] : null;
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;

// Check for valid input
if (is_null($component_id) || is_null($user_id) || is_null($quantity)) {
    die("Error: Invalid input.");
}

try {
    //Add back to components
    $update = "UPDATE inventory SET quantity = quantity + ? WHERE component_id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ii", $quantity, $component_id);
    $stmt->execute();
    if ($stmt->affected_rows == 0) {
        throw new Exception("No rows updated in inventory, component might not exist.");
    }

    // Add return transaction
    $insert = "INSERT INTO transactions (component_id, user_id, quantity, transaction_type, transaction_date) VALUES (?, ?, ?, 'return', NOW())";
    $stmt2 = $conn->prepare($insert);
    $stmt2->bind_param("iii", $component_id, $user_id, $quantity);
    $stmt2->execute();
    if ($stmt2->affected_rows == 0) {
        throw new Exception("No rows inserted in transactions.");
    }

    $conn->commit();
    echo "success";
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

if (isset($stmt)) $stmt->close();
if (isset($stmt2)) $stmt2->close();
$conn->close();

?>