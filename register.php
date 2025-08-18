<?php
$servername = "localhost";
$Username = "root"; // MySQL username
$Password = "";     // MySQL password
$dbname = "frisup";

// Connect to MySQL
$conn = new mysqli($servername, $Username, $Password, $dbname);
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$email = $data['email'];
$contact = $data['contact'];
$address = $data['address'];
$passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

// Check if email already exists
$sql = "SELECT * FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
} else {
    $sql = "INSERT INTO users (name, email, contact, address, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $contact, $address, $passwordHash);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registered successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed"]);
    }
}

$conn->close();
?>
