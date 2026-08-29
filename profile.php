<?php
// profile.php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) redirect('login.php');
$user_id = $_SESSION['user_id'];
$user = getUserById($conn, $user_id);

// Get watch history (last watched)
$history_stmt = $conn->prepare("
    SELECT m.id, m.title, m.poster_image, um.last_watched_at, um.rating, um.is_favorite
    FROM user_movie um
    JOIN movies m ON um.movie_id = m.id
    WHERE um.user_id = ? AND um.is_watched = 1
    ORDER BY um.last_watched_at DESC
");
$history_stmt->bind_param("i", $user_id);
$history_stmt->execute();
$history = $history_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get favorites
$fav_stmt = $conn->prepare("
    SELECT m.id, m.title, m.poster_image
    FROM user_movie um
    JOIN movies m ON um.movie_id = m.id
    WHERE um.user_id = ? AND um.is_favorite = 1
");
$fav_stmt->bind_param("i", $user_id);
$fav_stmt->execute();
$favorites = $fav_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>

<section>
    <h3>Continue Watching</h3>
    <div class="grid">
        <?php if (empty($history)): ?>
            <p>No watch history yet. <a href="index.php">Browse movies</a>.</p>
        <?php else: ?>
            <?php foreach ($history as $item): ?>
                <div class="movie-card">
                    <a href="movie.php?id=<?= $item['id'] ?>">
                        <img src="<?= htmlspecialchars($item['poster_image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                        <h4><?= htmlspecialchars($item['title']) ?></h4>
                        <small>Last watched: <?= date('M d, Y', strtotime($item['last_watched_at'])) ?></small>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section>
    <h3>Your Favorites</h3>
    <div class="grid">
        <?php if (empty($favorites)): ?>
            <p>No favorites yet. Start adding movies to your favorites!</p>
        <?php else: ?>
            <?php foreach ($favorites as $fav): ?>
                <div class="movie-card">
                    <a href="movie.php?id=<?= $fav['id'] ?>">
                        <img src="<?= htmlspecialchars($fav['poster_image']) ?>" alt="<?= htmlspecialchars($fav['title']) ?>">
                        <h4><?= htmlspecialchars($fav['title']) ?></h4>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>