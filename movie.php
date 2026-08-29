<?php
// movie.php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) redirect('index.php');
$movie_id = intval($_GET['id']);
$movie = getMovie($conn, $movie_id);
if (!$movie) redirect('index.php');

$user_id = $_SESSION['user_id'] ?? null;
$interaction = $user_id ? getUserMovieInteraction($conn, $user_id, $movie_id) : null;

include 'includes/header.php';
?>

<div class="movie-detail">
    <div class="movie-poster">
        <img src="<?= htmlspecialchars($movie['poster_image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
    </div>
    <div class="movie-info">
        <h2><?= htmlspecialchars($movie['title']) ?></h2>
        <p><strong>Year:</strong> <?= $movie['release_year'] ?? 'N/A' ?></p>
        <p><strong>Duration:</strong> <?= $movie['duration_minutes'] ?> min</p>
        <p><strong>Category:</strong> <?= htmlspecialchars($movie['category_name'] ?? 'Uncategorized') ?></p>
        <p><?= nl2br(htmlspecialchars($movie['description'])) ?></p>

        <div class="actions">
            <a href="watch.php?id=<?= $movie['id'] ?>" class="btn">▶ Watch Now</a>

            <?php if ($user_id): ?>
                <!-- Favorite toggle -->
                <button class="fav-btn" data-movie="<?= $movie['id'] ?>">
                    <?= ($interaction && $interaction['is_favorite']) ? '❤️ Unfavorite' : '🤍 Favorite' ?>
                </button>
                <!-- Rating (simplified) -->
                <div class="rating">
                    <span>Your rating: </span>
                    <select id="rating-select" data-movie="<?= $movie['id'] ?>">
                        <option value="">Rate</option>
                        <?php for ($i=1; $i<=10; $i++): ?>
                            <option value="<?= $i ?>" <?= ($interaction && $interaction['rating'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            <?php else: ?>
                <p><a href="login.php">Login</a> to favorite or rate this movie.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>