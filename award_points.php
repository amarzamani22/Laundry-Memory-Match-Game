<?php
session_start();
require 'config.php'; // Connect to DB
header('Content-Type: application/json');

// 1. Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$points = intval($data['points'] ?? 0);

// 2. Validate points
if ($points <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid point value.']);
    exit();
}

// 3. Update user points
$update = mysqli_query($conn, "UPDATE users SET points = points + $points WHERE user_id = $userId");

if ($update) {
    echo json_encode([
        'success' => true,
        'message' => "🎉 $points points added successfully!"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database update failed.'
    ]);
}
?>
