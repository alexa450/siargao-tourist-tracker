<?php
header("Content-Type: application/json");
require 'db.php';

$fullname = trim(string: $_POST['signupName'] ?? '');
$email = trim(string: $_POST['signupEmail'] ?? '');
$password = $_POST['signupPassword'] ?? '';

// Validate input
if ($fullname === '' || $email === '' || $password === '') {
    echo json_encode(value: ["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare(query: "SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(value: ["status" => "error", "message" => "Email is already registered."]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$stmt = $conn->prepare(query: "INSERT INTO users (username, email, fullname, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $email, $email, $fullname, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(value: ["status" => "success", "message" => "Signup successful."]);
} else {
    echo json_encode(value: ["status" => "error", "message" => "Signup failed. Try again."]);
}

$stmt->close();
$conn->close();
?>