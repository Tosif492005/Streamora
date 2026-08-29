<?php
// ajax/rate.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['movie_id']) || !isset($_POST['rating'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$movie_id = intval($_POST['movie_id']);
$rating = intval($_POST['rating']);
if ($rating < 1 || $rating > 10) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 10']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM user_movie WHERE user_id = ? AND movie_id = ?");
$stmt->bind_param("ii", $user_id, $movie_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE user_movie SET rating = ? WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("iii", $rating, $user_id, $movie_id);
} else {
    $stmt = $conn->prepare("INSERT INTO user_movie (user_id, movie_id, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $movie_id, $rating);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}