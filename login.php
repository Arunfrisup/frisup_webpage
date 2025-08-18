<?php
$servername = "localhost";
$dbUsername = "root";  // ✅ better variable name
$dbPassword = "";
$dbname = "frisup";

// Connect to MySQL
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) { 
    die(json_encode(["status" => "error", "message" => "DB connection failed"]));
}

// Read JSON input from fetch()
$data = json_decode(file_get_contents("php://input"), true);

$login = $data['login'] ?? '';      // username OR email
$password = $data['password'] ?? '';

if (empty($login) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit;
}

// ✅ Check both username and email
$sql = "SELECT * FROM users WHERE name = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $login, $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "not_found"]);
} else {
    $user = $result->fetch_assoc();

    // ✅ Verify hashed password (from register.php)
    if (password_verify($password, $user['password'])) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "wrong_password"]);
    }
}

$stmt->close();
$conn->close();
?>
