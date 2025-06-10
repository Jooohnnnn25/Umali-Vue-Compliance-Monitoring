<?php
// ====================================================================================
// SECTION 1: Configuration, CORS Headers, and Database Connection
// ====================================================================================

// Database credentials
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Your database password, if any
define('DB_NAME', 'bopems_db');

// Define the base URL for uploaded files (if still used elsewhere, otherwise can be removed)
// This constant isn't directly used in this specific file's current functions,
// but is often useful for other PHP scripts that might serve file URLs.
define('UPLOAD_BASE_URL', 'http://localhost/compliance-monitoring-vue-umali/uploads/');


// Set CORS headers
// THESE ARE CRITICAL FOR ALLOWING YOUR VUE.JS FRONTEND TO ACCESS THIS SCRIPT
header("Access-Control-Allow-Origin: *"); // Allows requests from any origin (for development)
header("Access-Control-Allow-Methods: GET, OPTIONS, POST"); // Explicitly allow GET, OPTIONS, POST methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specified headers (Content-Type for JSON, Authorization if you add tokens later)
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
// Browsers send an OPTIONS request before the actual GET/POST if it's a "non-simple" request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(); // End script execution for OPTIONS requests
}

// Function to establish database connection
function connect_db() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        // Log the error internally, but provide a generic message to the frontend
        error_log("Database connection failed: " . $conn->connect_error);
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Database connection failed."]);
        exit(); // Terminate script execution
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
// SECTION 2: API Endpoint Functions
// ====================================================================================

// Function to fetch all applications for the dashboard
function handleGetApplications($conn) {
    // --- ORIGINAL QUERY (WITH JOIN) ---
    // This query fetches cpdo_permits and joins with users to get the username.
    // Ensure 'cpdo_permits' and 'users' tables exist and 'user_id' column is correct.
    $sql = "SELECT p.id, p.application_number, p.date_received, p.professionals, p.status, u.username AS user
            FROM cpdo_permits p  -- Changed from 'permits' to 'cpdo_permits'
            JOIN users u ON p.user_id = u.id";

    // --- TEST QUERY (UNCOMMENT TO USE FOR DEBUGGING) ---
    // If you are getting 'undefined' or a SQL error, try this simpler query first.
    // It removes the JOIN to isolate if the issue is with the 'users' table or the JOIN condition.
    // $sql = "SELECT id, application_number, date_received, professionals, status FROM cpdo_permits LIMIT 10"; // Changed from 'permits' to 'cpdo_permits'


    $result = $conn->query($sql);

    // Check for SQL query execution errors
    if ($result === false) {
        error_log("SQL Error in handleGetApplications: " . $conn->error);
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Database query failed: " . $conn->error]);
        return; // Important to return here to prevent further execution
    }

    $applications = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $applications[] = [
                'id' => $row['id'],
                'applicationNumber' => $row['application_number'],
                'dateReceived' => $row['date_received'] ? date('m/d/Y', strtotime($row['date_received'])) : 'N/A',
                'professionals' => $row['professionals'],
                'user' => $row['user'] ?? 'N/A', // Use null coalescing to handle cases where 'user' might be missing if you switch queries
                'status' => $row['status']
            ];
        }
        echo json_encode(["success" => true, "applications" => $applications]);
    } else {
        echo json_encode(["success" => true, "applications" => [], "message" => "No applications found."]);
    }
}

// Function to fetch remarks for a given application ID
function handleGetApplicationRemarks($conn, $application_id) {
    if (!isset($application_id) || !is_numeric($application_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing or invalid application_id."]);
        return;
    }

    $sql = "SELECT id, remark_text, remark_by, created_at FROM application_remarks WHERE application_id = ? ORDER BY created_at ASC";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("SQL Prepare Error for remarks: " . $conn->error);
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to prepare remarks query."]);
        return;
    }

    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $remarks = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $remarks[] = $row;
        }
        echo json_encode(["success" => true, "remarks" => $remarks]);
    } else {
        echo json_encode(["success" => true, "remarks" => [], "message" => "No remarks found for this application."]);
    }
    $stmt->close();
}

// Function to update application status or add remarks
function handleUpdateApplication($conn) {
    // Read raw POST data (Axios sends JSON in the body)
    $data = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON payload."]);
        return;
    }

    $action = $data['action'] ?? ''; // Expects 'updateApplication'
    $id = $data['id'] ?? null;

    if (!isset($id) || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing or invalid application ID."]);
        return;
    }

    if (isset($data['status'])) { // If status is provided, update status
        $status = $data['status'];
        $sql = "UPDATE cpdo_permits SET status = ? WHERE id = ?"; // Changed from 'permits' to 'cpdo_permits'
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("SQL Prepare Error for status update: " . $conn->error);
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to prepare status update query."]);
            return;
        }
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Application status updated successfully."]);
        } else {
            error_log("SQL Execute Error for status update: " . $stmt->error);
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to update status: " . $stmt->error]);
        }
        $stmt->close();
    } elseif (isset($data['remarks'])) { // If remarks is provided, insert new remark
        $remarks = trim($data['remarks']); // Trim whitespace from remarks
        if (empty($remarks)) { // Prevent saving empty remarks
            echo json_encode(["success" => false, "message" => "Remarks cannot be empty."]);
            return;
        }
        // You'll need to define who is making the remark (e.g., from session or a fixed value for now)
        $remark_by = "CPDO Staff"; // Replace with dynamic user if user authentication is implemented
        
        $sql = "INSERT INTO application_remarks (application_id, remark_text, remark_by) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("SQL Prepare Error for remarks insert: " . $conn->error);
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to prepare remarks insert query."]);
            return;
        }
        $stmt->bind_param("iss", $id, $remarks, $remark_by);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Remarks added successfully."]);
        } else {
            error_log("SQL Execute Error for remarks insert: " . $stmt->error);
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to add remarks: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "No valid action (status or remarks) provided for update."]);
    }
}

// ====================================================================================
// SECTION 3: Request Router
// ====================================================================================

$conn = connect_db(); // Establish database connection

// Determine the request method and action
$action = $_GET['action'] ?? ''; // For GET requests
$request_method = $_SERVER['REQUEST_METHOD'];

// Handle POST requests for updates first
if ($request_method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON payload."]);
        close_db($conn);
        exit();
    }

    if (isset($data['action']) && $data['action'] === 'updateApplication') {
        handleUpdateApplication($conn);
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid or missing POST action."]);
    }
} elseif ($request_method === 'GET') {
    // Handle GET requests based on 'action' parameter
    switch ($action) {
        case 'getApplications':
            handleGetApplications($conn);
            break;
        case 'getApplicationRemarks':
            $application_id = $_GET['id'] ?? null;
            handleGetApplicationRemarks($conn, $application_id);
            break;
        default:
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid or missing GET action."]);
            break;
    }
} else {
    // Handle other HTTP methods not allowed
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
}

close_db($conn); // Close database connection
?>