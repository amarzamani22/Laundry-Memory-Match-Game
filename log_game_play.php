<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$game = $_POST['game'] ?? '';
$today = date('Y-m-d');

if (!$game) {
    http_response_code(400);
    echo json_encode(['error' => 'Game not specified']);
    exit();
}

// Check today's play count for this game
$check = mysqli_query($conn, "
    SELECT play_count FROM game_plays 
    WHERE user_id = $user_id 
    AND game_name = '$game' 
    AND play_date = '$today'
");

if ($check && mysqli_num_rows($check) > 0) {
    $row = mysqli_fetch_assoc($check);
    if ($row['play_count'] < 3) {
        // Only update if play count is still below 3
        mysqli_query($conn, "
            UPDATE game_plays 
            SET play_count = play_count + 1 
            WHERE user_id = $user_id 
            AND game_name = '$game' 
            AND play_date = '$today'
        ");
    }
} else {
    // First time playing today, insert a new row
    mysqli_query($conn, "
        INSERT INTO game_plays (user_id, game_name, play_date, play_count) 
        VALUES ($user_id, '$game', '$today', 1)
    ");
}

echo json_encode(['success' => true]);
?>
