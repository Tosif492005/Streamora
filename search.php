<?php
// search.php
require_once 'config/database.php';
require_once 'includes/functions.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

include 'includes/header.php';
?>

<section class="search-results">
    <h2>Search Results for: <em><?= htmlspecialchars($query) ?></em></h2>

    <?php if (empty($query)): ?>
        <p>Please enter a search term.</p>
    <?php else: ?>
        <?php
        $stmt = $conn->prepare("SELECT id, title, poster_image, release_year FROM movies WHERE title LIKE ? ORDER BY title ASC");
        $searchTerm = "%" . $query . "%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $movies = $result->fetch_all(MYSQLI_ASSOC);
        ?>

        <?php if (count($movies) > 0): ?>
            <div class="grid">
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <a href="movie.php?id=<?= $movie['id'] ?>">
                            <img src="<?= htmlspecialchars($movie['poster_image'] ?: 'assets/images/no-poster.jpg') ?>" 
                                 alt="<?= htmlspecialchars($movie['title']) ?>">
                            <h4><?= htmlspecialchars($movie['title']) ?></h4>
                            <span><?= $movie['release_year'] ?? '' ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results">😞 No movies found matching "<strong><?= htmlspecialchars($query) ?></strong>".</p>
            <p><a href="index.php">Browse all movies</a></p>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>