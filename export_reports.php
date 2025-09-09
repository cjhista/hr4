<?php
// export_reports.php

// Example payroll data (replace with DB later)
$payrollData = [
    '2025-04' => [
        [
            "name" => "Charles Dela Cruz",
            "position" => "HR Manager",
            "base_salary" => 45000,
            "overtime" => 2000,
            "bonuses" => 3000,
            "deductions" => ["Tax" => 5000, "SSS" => 1200],
            "status" => "Paid"
        ],
        [
            "name" => "Andrea Santos",
            "position" => "Accountant",
            "base_salary" => 38000,
            "overtime" => 1500,
            "bonuses" => 2000,
            "deductions" => ["Tax" => 4000, "PhilHealth" => 1000],
            "status" => "Paid"
        ]
    ],
    '2025-07' => [
        [
            "name" => "Michael Reyes",
            "position" => "IT Specialist",
            "base_salary" => 40000,
            "overtime" => 2500,
            "bonuses" => 2500,
            "deductions" => ["Tax" => 4500, "Pag-IBIG" => 800],
            "status" => "Unpaid"
        ]
    ],
    '2025-09' => [
        [
            "name" => "Charles Dela Cruz",
            "position" => "HR Manager",
            "base_salary" => 45000,
            "overtime" => 3000,
            "bonuses" => 4000,
            "deductions" => ["Tax" => 5200, "SSS" => 1300],
            "status" => "Paid"
        ],
        [
            "name" => "Andrea Santos",
            "position" => "Accountant",
            "base_salary" => 38000,
            "overtime" => 1800,
            "bonuses" => 2500,
            "deductions" => ["Tax" => 4200, "PhilHealth" => 1100],
            "status" => "Paid"
        ]
    ]
];


$selectedMonth = $_GET['month'] ?? date('Y-m');
if (!isset($payrollData[$selectedMonth])) {
    die("No payroll data available for this month.");
}
$data = $payrollData[$selectedMonth];


$filename = "Payroll_Report_" . date("F_Y", strtotime($selectedMonth)) . ".csv";


header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$filename\"");

// Open output stream
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['Name', 'Position', 'Base Salary (₱)', 'Overtime (₱)', 'Bonuses (₱)', 'Deductions (₱)', 'Net Pay (₱)', 'Status']);

// Write data rows
foreach ($data as $p) {
    $gross = $p['base_salary'] + $p['overtime'] + $p['bonuses'];
    $deductions = array_sum($p['deductions']);
    $net = $gross - $deductions;

    fputcsv($output, [
        $p['name'],
        $p['position'],
        number_format($p['base_salary'], 2),
        number_format($p['overtime'], 2),
        number_format($p['bonuses'], 2),
        number_format($deductions, 2),
        number_format($net, 2),
        $p['status']
    ]);
}

fclose($output);
exit;