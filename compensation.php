<?php
$pageTitle = "HR 4 Compensation";
$userName  = "User"; 
$hotelName = "Hotel & Restaurant NAME";

// Database connection
$host = "localhost:3307"; 
$user = "root"; 
$pass = ""; 
$db   = "hr4_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Summary Query
$sqlSummary = "
  SELECT 
    SUM(c.salary) AS totalComp,
    AVG(c.salary) AS avgSalary,
    SUM(CASE WHEN c.next_review <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS reviewsDue,
    SUM(CASE WHEN c.rating >= 4.5 THEN 1 ELSE 0 END) AS highPerformers
  FROM compensation c
";
$summary = $conn->query($sqlSummary)->fetch_assoc();

$totalCompensation = "₱" . number_format($summary['totalComp'] ?? 0, 2);
$averageSalary     = "₱" . number_format($summary['avgSalary'] ?? 0, 2);
$reviewsDue        = $summary['reviewsDue'] ?? 0;
$highPerformers    = $summary['highPerformers'] ?? 0;

// Employee Compensation Data
$sqlEmployees = "
  SELECT e.id, CONCAT(e.first_name, ' ', e.last_name) AS name, e.position AS role,
         c.salary, c.rating, c.last_increase, c.last_increase_date,
         c.market_min, c.market_max, c.median, c.next_review
  FROM employees e
  JOIN compensation c ON e.id = c.employee_id
";
$result = $conn->query($sqlEmployees);

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "role" => $row['role'],
        "salary" => "₱" . number_format($row['salary'], 2),
        "rating" => $row['rating'] . "/5.0",
        "lastIncrease" => "₱" . number_format($row['last_increase'], 2),
        "lastIncreaseDate" => $row['last_increase_date'],
        "marketRange" => "₱" . number_format($row['market_min'], 0) . " - ₱" . number_format($row['market_max'], 0),
        "median" => "₱" . number_format($row['median'], 0),
        "nextReview" => $row['next_review'],
        "marketPos" => ($row['median'] > 0) 
            ? round(($row['salary'] / $row['market_max']) * 100) 
            : 0
    ];
}

// Helper function for rating color
function getRatingColor($rating) {
  $value = floatval($rating); 
  if ($value >= 4.0) {
    return "text-green-600 font-semibold";
  } elseif ($value >= 3.0) {
    return "text-yellow-600 font-semibold";
  } else {
    return "text-red-600 font-semibold";
  }
}
?>
