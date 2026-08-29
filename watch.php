<?php
// watch.php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) redirect('index.php');
$movie_id = intval($_GET['id']);
$movie = getMovie($conn, $movie_id);
if (!$movie) redirect('index.php');

// If user logged in, record that they watched it
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Check if already exists
    $stmt = $conn->prepare("SELECT id FROM user_movie WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        // Update watch status and last_watched_at
        $stmt = $conn->prepare("UPDATE user_movie SET is_watched = 1, last_watched_at = NOW() WHERE user_id = ? AND movie_id = ?");
        $stmt->bind_param("ii", $user_id, $movie_id);
        $stmt->execute();
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO user_movie (user_id, movie_id, is_watched, last_watched_at) VALUES (?, ?, 1, NOW())");
        $stmt->bind_param("ii", $user_id, $movie_id);
        $stmt->execute();
    }
}

include 'includes/header.php';
?>

<div class="watch-container">
    <h2><?= htmlspecialchars($movie['title']) ?></h2>
    <video controls autoplay width="100%" style="max-width: 900px; display: block; margin: 0 auto;">
        <source src="<?= htmlspecialchars($movie['video_url']) ?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <p><a href="movie.php?id=<?= $movie['id'] ?>">← Back to movie details</a></p>
</div>

<?php include 'includes/footer.php'; ?>