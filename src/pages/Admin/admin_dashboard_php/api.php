<?php
// ====================================================================================
// SECTION 1: Configuration and CORS Headers
// ====================================================================================

// Database credentials
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Your database password, if any
define('DB_NAME', 'bopems_db');

// Set CORS headers
header("Access-Control-Allow-Origin: *"); // FOR DEVELOPMENT: Allows requests from any origin
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Allow necessary HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers for requests
header("Content-Type: application/json; charset=UTF-8"); // Specify JSON response

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(); // Stop script execution after sending preflight headers
}

// Function to establish database connection
function connect_db() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        // Return an error JSON if connection fails
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
        exit();
    }
    return $conn;
}

// Function to close database connection
function close_db($conn) {
    if ($conn) {
        $conn->close();
    }
}

// ====================================================================================
// SECTION 2: API Endpoints (Functions)
// ====================================================================================

// Function to fetch applications
function handleGetApplications($conn, $searchQuery) {
    // Prepare SQL query
    $sql = "SELECT id, date_received, application_number, professionals, user, status, remarks FROM permits";

    // Add search condition if search query is provided
    if (!empty($searchQuery)) {
        // Using prepared statement for search as well for better security, even with LIKE
        $sql .= " WHERE date_received LIKE ? OR " .
                "application_number LIKE ? OR " .
                "professionals LIKE ? OR " .
                "user LIKE ? OR " .
                "status LIKE ? OR " .
                "remarks LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchParam = "%" . $searchQuery . "%";
        $stmt->bind_param("ssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }

    $applications = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Ensure remarks is not null, converting to empty string for frontend consistency
            if ($row['remarks'] === null) {
                $row['remarks'] = '';
            }
            $applications[] = $row;
        }
        echo json_encode(["success" => true, "applications" => $applications]);
    } else {
        echo json_encode(["success" => true, "applications" => []]); // Return empty array if no results
    }

    if (isset($stmt)) {
        $stmt->close();
    }
}

// Function to update application status
function handleUpdateApplicationStatus($conn, $data) {
    if (isset($data['id']) && isset($data['status'])) {
        $id = $data['id'];
        $status = $data['status'];

        // Using prepared statement for security
        $stmt = $conn->prepare("UPDATE permits SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id); // "si" means string, integer

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Application status updated successfully."]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["success" => false, "message" => "Error updating status: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Missing 'id' or 'status' in request."]);
    }
}

// Function to update application remarks
function handleUpdateApplicationRemarks($conn, $data) {
    if (isset($data['id']) && array_key_exists('remarks', $data)) { // Check for existence as remarks can be empty string
        $id = $data['id'];
        $remarks = $data['remarks'];

        // Using prepared statement for security
        $stmt = $conn->prepare("UPDATE permits SET remarks = ? WHERE id = ?");
        $stmt->bind_param("si", $remarks, $id); // "si" means string, integer

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Application remarks updated successfully."]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["success" => false, "message" => "Error updating remarks: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Missing 'id' or 'remarks' in request."]);
    }
}


// ====================================================================================
// SECTION 3: Request Router (Main Logic)
// ====================================================================================

$conn = connect_db(); // Establish database connection at the beginning

// Determine the action based on request method and parameters
$action = '';
$request_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_data = json_decode(file_get_contents("php://input"), true); // Decode as associative array
    $action = $request_data['action'] ?? ''; // Get 'action' from the POST body
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? ''; // Get 'action' from the GET URL parameter
}

switch ($action) {
    case 'getApplications':
        $searchQuery = $_GET['search'] ?? ''; // Get search query from GET for getApplications
        handleGetApplications($conn, $searchQuery);
        break;

    case 'updateStatus':
        handleUpdateApplicationStatus($conn, $request_data);
        break;

    case 'updateRemarks':
        handleUpdateApplicationRemarks($conn, $request_data);
        break;

    default:
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Invalid or missing action."]);
        break;
}

close_db($conn); // Close database connection at the end
?>