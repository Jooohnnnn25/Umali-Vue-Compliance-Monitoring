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

// Define the static data for documents
$generalDocuments = [
    [ "documentType" => "Unified Application Form", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Ancillary Permit Forms", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Architectural Documents", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Civil/Structural Documents", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Electrical Documents", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Sanitary Documents", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Plumbing Documents", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Mechanical Documents", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Electronics Documents", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Geodetic Documents", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Lot Plan", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Photocopies of Valid Licenses", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Notarized estimated value", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Technical Specifications", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Structural Design and Seismic Analysis", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Plate Load Test Result", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Soil Boring Test Result", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
];

$proofOfOwnershipDocuments = [
    [ "documentType" => "Original Certificate of Title (OCT)", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Transfer Certificate Title (TCT)", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Deed of Absolute Sale", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Tax Declaration", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Tax Receipt", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
];

$clearanceDocuments = [
    [ "documentType" => "Construction Safety and Health Program", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Fire Safety Evaluation Clearance", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Locational Clearance", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "DPWH Clearance", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
    [ "documentType" => "Barangay Clearance", "dateSubmitted" => "04/05/2025", "status" => "Submitted" ],
];

// Combine all document types into a single response object
$response = [
    "success" => true,
    "generalDocuments" => $generalDocuments,
    "proofOfOwnershipDocuments" => $proofOfOwnershipDocuments,
    "clearanceDocuments" => $clearanceDocuments
];

// Output the JSON response
echo json_encode($response);

?>