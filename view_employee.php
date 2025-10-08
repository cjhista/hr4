<?php
include 'db.php';
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($row = $result->fetch_assoc()){
        echo "
        <div class='space-y-2'>
          <p><strong>Name:</strong> {$row['first_name']} {$row['last_name']}</p>
          <p><strong>Email:</strong> {$row['email']}</p>
          <p><strong>Phone:</strong> {$row['phone']}</p>
          <p><strong>Department:</strong> {$row['department']}</p>
          <p><strong>Position:</strong> {$row['position']}</p>
          <p><strong>Status:</strong> {$row['status']}</p>
          <p><strong>Salary:</strong> ₱".number_format($row['salary'],2)."</p>
        </div>
        <div class='flex justify-end mt-4'>
          <button type='button' onclick='closeModal()' class='px-4 py-2 border rounded-lg'>Close</button>
        </div>";
    }
}
?>
