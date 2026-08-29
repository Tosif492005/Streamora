<?php
// includes/functions.php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

// Get user data by ID
function getUserById($conn, $id) {
    $stmt = $conn->prepare("SELECT id, username, email, avatar FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get movie details
function getMovie($conn, $id) {
    $stmt = $conn->prepare("SELECT m.*, c.name as category_name 
                            FROM movies m 
                            LEFT JOIN categories c ON m.category_id = c.id 
                            WHERE m.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get user's interaction with a movie (rating, favorite, watched)
function getUserMovieInteraction($conn, $user_id, $movie_id) {
    $stmt = $conn->prepare("SELECT rating, is_watched, is_favorite FROM user_movie 
                            WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc(); // returns null if no row
}

// Get all movies (for homepage)
function getAllMovies($conn) {
    $result = $conn->query("SELECT id, title, poster_image, release_year, is_featured 
                            FROM movies ORDER BY created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get featured movies
function getFeaturedMovies($conn) {
    $result = $conn->query("SELECT id, title, poster_image, backdrop_image, description 
                            FROM movies WHERE is_featured = 1 LIMIT 5");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get categories (for filter)
function getAllCategories($conn) {
    $result = $conn->query("SELECT id, name FROM categories ORDER BY name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Movies by category
function getMoviesByCategory($conn, $category_id) {
    $stmt = $conn->prepare("SELECT id, title, poster_image, release_year 
                            FROM movies WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>