<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- TEMPORARY DEBUGGING CODE ---
// Uncomment the following two lines to test if your frontend receives a message.
// If it does, the issue is further down in your original PHP logic.
// echo json_encode(["success" => false, "message" => "PHP reached, forced test error!"]);
// exit();
// --- END TEMPORARY DEBUGGING CODE ---


// Database credentials
$servername = "127.0.0.1";
$username_db = "root";
$password_db = ""; // Your database password, if any
$dbname = "bopems_db"; // You should create this database

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    // This message should be properly returned.
    die(json_encode(["success" => false, "message" => "DB Connection failed: " . $conn->connect_error]));
}

// Get the POST data
$data = json_decode(file_get_contents("php://input"));

if (isset($data->username) && isset($data->password) && isset($data->selectedRole)) {
    $username = $conn->real_escape_string($data->username);
    $password = $conn->real_escape_string($data->password);
    $selectedRole = $conn->real_escape_string($data->selectedRole);

    // SQL query to check user credentials and role
    $sql = "SELECT id, username, role FROM users WHERE username = '$username' AND role = '$selectedRole' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "message" => "Login successful!",
            "user" => [
                "id" => $row['id'],
                "username" => $row['username'],
                "role" => $row['role']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid username, password, or role."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing username, password, or role in POST data."]);
}

$conn->close();
?>