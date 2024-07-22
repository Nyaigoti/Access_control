<?php
include 'connectDB.php';

if (isset($_POST['component_id'])) {
    $component_id = $_POST['component_id'];

    // Prepare the SQL statement to avoid SQL injection
    $sql = "SELECT u.username, SUM(CASE WHEN t.transaction_type = 'assign' THEN t.quantity ELSE -t.quantity END) AS quantity
            FROM users u
            LEFT JOIN transactions t ON u.id = t.user_id AND t.component_id = ?
            GROUP BY u.username
            HAVING SUM(CASE WHEN t.transaction_type = 'assign' THEN t.quantity ELSE -t.quantity END) > 0";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("i", $component_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $table_data = '<table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>';
        while ($row = $result->fetch_assoc()) {
            $table_data .= '<tr>
                                <td>' . htmlspecialchars($row['username']) . '</td>
                                <td>' . htmlspecialchars($row['quantity']) . '</td>
                            </tr>';
        }
        $table_data .= '</tbody></table>';

        echo $table_data;
    } else {
        echo '<p>No users found with transactions greater than zero for this component.</p>';
    }

    $stmt->close();
} else {
    echo '<p>Component ID not provided.</p>';
}

$conn->close();
?>
