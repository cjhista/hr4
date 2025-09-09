<?php
include 'db.php'; // DB connection

$pageTitle = "HR 4 Compensation";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME";

// Example Compensation Summary (pwede mong gawin dynamic later)
$totalCompensation = "₱1,512,000";
$averageSalary     = "₱31,500";
$reviewsDue        = 4;
$highPerformers    = 2;

// Get employees from DB
$sql = "SELECT * FROM employees WHERE status='Active'";
$result = $conn->query($sql);

$employees = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $employees[] = [
      "id" => $row['id'],
      "name" => $row['first_name'] . " " . $row['last_name'],
      "role" => $row['role'],
      "salary" => "₱" . number_format($row['salary'], 0),
      "rating" => $row['rating'] . "/5.0",
      "lastIncrease" => "₱" . number_format($row['last_increase'], 0),
      "lastIncreaseDate" => $row['last_increase_date'],
      "marketRange" => "₱25,000 - ₱50,000", // placeholder
      "median" => "₱35,000",                // placeholder
      "nextReview" => $row['next_review'],
      "marketPos" => rand(30, 70)           // dummy % for now
    ];
  }
}

// Helper function for rating color
function getRatingColor($rating) {
  $value = floatval(substr($rating, 0, 3)); 
  if ($value >= 4.0) {
    return "text-green-600 font-semibold";
  } elseif ($value >= 3.0) {
    return "text-yellow-600 font-semibold";
  } else {
    return "text-red-600 font-semibold";
  }
}
?>
