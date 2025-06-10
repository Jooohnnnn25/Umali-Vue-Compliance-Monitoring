<?php
// Set CORS headers
header("Access-Control-Allow-Origin: *"); // Allows requests from any origin (for development)
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Allow GET and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type"); // Allow Content-Type header
header("Content-Type: application/json; charset=UTF-8"); // Specify JSON response

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(); // Stop script execution after sending preflight headers
}

// Define the static data for Building Permit Monitoring
$buildingPermits = [
    [
        "id" => 1,
        "applicantName" => "Laurence Francisco (BP-2025-001-C)",
        "dateApplied" => "2025-06-01",
        "buildingPermit" => "Completed",
        "buildingPlans" => "Completed",
        "clearances" => "In Progress",
        "remarks" => "Pending Clearances",
        "daysRemaining" => "2 Days Remaining",
    ],
    [
        "id" => 2,
        "applicantName" => "Aaron James Cortez (BP-2025-001-C)",
        "dateApplied" => "2025-06-01",
        "buildingPermit" => "Completed",
        "buildingPlans" => "In Progress",
        "clearances" => "In Progress",
        "remarks" => "Pending Clearances & Plans",
        "daysRemaining" => "4 Days Remaining",
    ],
    [
        "id" => 3,
        "applicantName" => "Jomar Cerda (BP-2025-001-C)",
        "dateApplied" => "2025-06-01",
        "buildingPermit" => "Completed",
        "buildingPlans" => "Completed",
        "clearances" => "Completed",
        "remarks" => "", // No remarks for this entry
        "daysRemaining" => "-",
    ],
];

// Define the static data for Occupancy Monitoring
$occupancyMonitoring = [
    [
        "id" => 1,
        "applicantName" => "Laurence Francisco (BP-2025-001-C)",
        "dateApplied" => "2025-06-01",
        "occupancy" => "In Progress",
        "remarks" => "Pending Requirements",
        "daysRemaining" => "2 Days Remaining",
    ],
];

// Combine all monitoring data into a single response object
$response = [
    "success" => true,
    "buildingPermits" => $buildingPermits,
    "occupancyMonitoring" => $occupancyMonitoring
];

// Output the JSON response
echo json_encode($response);

?>