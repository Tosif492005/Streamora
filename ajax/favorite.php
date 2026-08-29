<?php
// ajax/favorite.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['movie_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$movie_id = intval($_POST['movie_id']);
$action = $_POST['action']; // 'add' or 'remove'

// Check existing record
$stmt = $conn->prepare("SELECT id FROM user_movie WHERE user_id = ? AND movie_id = ?");
$stmt->bind_param("ii", $user_id, $movie_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update
    $is_favorite = ($action === 'add') ? 1 : 0;
    $stmt = $conn->prepare("UPDATE user_movie SET is_favorite = ? WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("iii", $is_favorite, $user_id, $movie_id);
    $success = $stmt->execute();
} else {
    // Insert new row
    $is_favorite = ($action === 'add') ? 1 : 0;
    $stmt = $conn->prepare("INSERT INTO user_movie (user_id, movie_id, is_favorite) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $movie_id, $is_favorite);
    $success = $stmt->execute();
}

if ($success) {
    echo json_encode(['success' => true, 'is_favorite' => $is_favorite]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}