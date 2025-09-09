<?php

$type   = $_GET['type'] ?? 'csv';
$filter = $_GET['filter'] ?? 'all';
$hotelName = "Hotel & Restaurant NAME"; 


// Example data (replace with DB query later)
$attendanceRecords = [
    ["name" => "Maria Santos", "position" => "Front Desk Manager", "check_in" => "08:00", "check_out" => "17:00", "total_hours" => "9h 0m", "status" => "PRESENT"],
    ["name" => "John Dela Cruz", "position" => "Head Chef", "check_in" => "09:15", "check_out" => "18:30", "total_hours" => "9h 15m", "status" => "LATE"],
    ["name" => "Sarah Wilson", "position" => "Housekeeping Supervisor", "check_in" => "-", "check_out" => "-", "total_hours" => "-", "status" => "ABSENT"],
    ["name" => "Robert Garcia", "position" => "Server", "check_in" => "07:45", "check_out" => "19:30", "total_hours" => "11h 45m", "status" => "OVERTIME"],
    ["name" => "Lisa Reyes", "position" => "Receptionist", "check_in" => "08:30", "check_out" => "17:30", "total_hours" => "9h 0m", "status" => "PRESENT"],
];


if ($filter !== 'all') {
    $attendanceRecords = array_filter($attendanceRecords, function ($row) use ($filter) {
        return strtoupper($row['status']) === strtoupper($filter);
    });
}

if ($type === 'csv') {
    $date = date("Y-m-d");
    $safeHotelName = strtolower(str_replace(" ", "_", $hotelName));
    $filename = $safeHotelName . "_attendance_" . strtolower($filter) . "_" . $date . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'"');


    $output = fopen("php://output", "w");
    fputcsv($output, ["Name", "Position", "Check In", "Check Out", "Total Hours", "Status"]);

    foreach ($attendanceRecords as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}



?>