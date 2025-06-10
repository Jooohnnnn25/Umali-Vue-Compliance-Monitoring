<?php
// ====================================================================================
// SECTION 1: Configuration, CORS Headers, and Database Connection
// ====================================================================================

// Database credentials
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Your database password, if any
define('DB_NAME', 'bopems_db');

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT"); // Allow PUT for updates
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Function to establish database connection
function connect_db() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
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
// SECTION 2: API Endpoint Functions
// ====================================================================================

// Function to fetch all applications (with optional search)
function handleGetApplications($conn) {
    $search_query = $_GET['searchQuery'] ?? '';

    // --- Changed table name to 'engr_permits' ---
    $sql = "SELECT id, date_applied, application_number, assigned_professional, applicant_name, status, remarks FROM engr_permits";
    $params = [];
    $types = "";

    if (!empty($search_query)) {
        $sql .= " WHERE ";
        $sql .= "application_number LIKE ? OR applicant_name LIKE ? OR assigned_professional LIKE ? OR status LIKE ?";
        $search_param = "%" . $search_query . "%";
        $params = [$search_param, $search_param, $search_param, $search_param];
        $types = "ssss";
    }

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $applications = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $applications[] = [
                'id' => $row['id'],
                'dateReceived' => $row['date_applied'] ? date('m/d/Y', strtotime($row['date_applied'])) : 'N/A',
                'applicationNumber' => $row['application_number'],
                'professionals' => $row['assigned_professional'],
                'user' => $row['applicant_name'],
                'status' => $row['status'],
                'remarks' => $row['remarks']
            ];
        }
        echo json_encode(["success" => true, "applications" => $applications]);
    } else {
        echo json_encode(["success" => true, "applications" => [], "message" => "No applications found."]);
    }
    $stmt->close();
}

// Function to update application status
function handleUpdateApplicationStatus($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'] ?? null;
    $status = $data['status'] ?? null;

    if (!isset($id) || !isset($status)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing application ID or status."]);
        return;
    }

    // --- Changed table name to 'engr_permits' ---
    $sql = "UPDATE engr_permits SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Status updated successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to update status: " . $stmt->error]);
    }
    $stmt->close();
}

// Function to update application remarks
function handleUpdateApplicationRemarks($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'] ?? null;
    $remarks = $data['remarks'] ?? null;

    if (!isset($id) || !isset($remarks)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing application ID or remarks."]);
        return;
    }

    // --- Changed table name to 'engr_permits' ---
    $sql = "UPDATE engr_permits SET remarks = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $remarks, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Remarks updated successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to update remarks: " . $stmt->error]);
    }
    $stmt->close();
}

// ====================================================================================
// SECTION 3: Request Router
// ====================================================================================

$conn = connect_db(); // Establish database connection

$action = $_GET['action'] ?? ''; // Default action if none specified via GET
$method = $_SERVER['REQUEST_METHOD'];

// For POST/PUT requests, get action from JSON body if not in GET params
if (($method === 'POST' || $method === 'PUT') && empty($action)) {
    $input_data = json_decode(file_get_contents("php://input"), true);
    $action = $input_data['action'] ?? '';
}

switch ($action) {
    case 'getApplications':
        handleGetApplications($conn);
        break;
    case 'updateApplicationStatus':
        if ($method === 'PUT' || $method === 'POST') {
            handleUpdateApplicationStatus($conn);
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Method not allowed for this action."]);
        }
        break;
    case 'updateApplicationRemarks':
        if ($method === 'PUT' || $method === 'POST') {
            handleUpdateApplicationRemarks($conn);
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Method not allowed for this action."]);
        }
        break;
    default:
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid or missing action."]);
        break;
}

close_db($conn); // Close database connection
?>