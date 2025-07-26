<?php
session_start();
header("Content-Type: application/json");
require 'db.php';

$email = trim($_POST['loginEmail'] ?? '');
$password = $_POST['loginPassword'] ?? '';

// Basic validation
if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email and password are required."]);
    exit;
}

// Get user by email
$stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE username = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    exit;
}

$user = $result->fetch_assoc();

// Verify password
if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    echo json_encode(["status" => "success", "message" => "Login successful."]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
}

$stmt->close();
$conn->close();
?>