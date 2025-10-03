<?php
header('Content-Type: application/json');
include 'db.php';

// Check if 'id' is provided
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = intval($_GET['id']);

    // Prepare and execute SQL query safely
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, phone, department, position, salary, status FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(["error" => "Employee not found"]);
        }
    } else {
        echo json_encode(["error" => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid ID"]);
}

$conn->close();
?>
