<?php
// wishlist.php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Must be logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get all favorited movies
$stmt = $conn->prepare("
    SELECT m.id, m.title, m.poster_image, m.release_year, m.description,
           um.created_at as added_at
    FROM user_movie um
    JOIN movies m ON um.movie_id = m.id
    WHERE um.user_id = ? AND um.is_favorite = 1
    ORDER BY um.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$favorites = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<section class="wishlist">
    <h2>❤️ My Wishlist</h2>
    <p class="wishlist-count"><?= count($favorites) ?> movie(s) saved</p>

    <?php if (empty($favorites)): ?>
        <div class="empty-wishlist">
            <p>You haven't added any movies to your wishlist yet.</p>
            <a href="index.php" class="btn">Browse Movies</a>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($favorites as $movie): ?>
                <div class="movie-card" data-id="<?= $movie['id'] ?>">
                    <a href="movie.php?id=<?= $movie['id'] ?>">
                        <img src="<?= htmlspecialchars($movie['poster_image'] ?: 'assets/images/no-poster.jpg') ?>" 
                             alt="<?= htmlspecialchars($movie['title']) ?>">
                        <h4><?= htmlspecialchars($movie['title']) ?></h4>
                        <span><?= $movie['release_year'] ?? '' ?></span>
                    </a>
                    <!-- Remove button -->
                    <button class="remove-wishlist" data-id="<?= $movie['id'] ?>">✕ Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<script>
// AJAX to remove from wishlist (no page reload)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.remove-wishlist').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const movieId = this.dataset.id;
            const card = this.closest('.movie-card');
            
            fetch('ajax/favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `movie_id=${movieId}&action=remove`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    card.style.transition = 'opacity 0.3s';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                    // Update count
                    const countSpan = document.querySelector('.wishlist-count');
                    let count = parseInt(countSpan.textContent);
                    count--;
                    countSpan.textContent = count + ' movie(s) saved';
                    if (count === 0) {
                        location.reload(); // show empty message
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => alert('Request failed.'));
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>