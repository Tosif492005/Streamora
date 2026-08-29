<?php
// index.php
require_once 'config/database.php';
require_once 'includes/functions.php';

$featured = getFeaturedMovies($conn);
$allMovies = getAllMovies($conn);
$categories = getAllCategories($conn);

include 'includes/header.php';
?>

<section class="hero">
    <?php if (!empty($featured)): ?>
        <div class="featured" style="background-image: url('<?= htmlspecialchars($featured[0]['backdrop_image']) ?>');">
            <div class="featured-content">
                <h2><?= htmlspecialchars($featured[0]['title']) ?></h2>
                <p><?= htmlspecialchars(substr($featured[0]['description'], 0, 150)) ?>...</p>
                <a href="movie.php?id=<?= $featured[0]['id'] ?>" class="btn">Watch Now</a>
            </div>
        </div>
    <?php endif; ?>
</section>

<section class="categories">
    <h3>Categories</h3>
    <ul>
        <?php foreach ($categories as $cat): ?>
            <li><a href="index.php?category=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</section>

<section class="movie-grid">
    <h3>All Movies</h3>
    <div class="grid">
        <?php 
        $movies = isset($_GET['category']) ? getMoviesByCategory($conn, intval($_GET['category'])) : $allMovies;
        foreach ($movies as $movie): ?>
            <div class="movie-card">
                <a href="movie.php?id=<?= $movie['id'] ?>">
                    <img src="<?= htmlspecialchars($movie['poster_image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                    <h4><?= htmlspecialchars($movie['title']) ?></h4>
                    <span><?= $movie['release_year'] ?? '' ?></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>