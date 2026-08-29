// assets/js/script.js
// Handle favorite toggle (AJAX)
document.addEventListener('DOMContentLoaded', function() {
    const favBtns = document.querySelectorAll('.fav-btn');
    favBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const movieId = this.dataset.movie;
            const action = this.textContent.trim().startsWith('❤️') ? 'remove' : 'add';
            fetch('ajax/favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `movie_id=${movieId}&action=${action}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.textContent = data.is_favorite ? '❤️ Unfavorite' : '🤍 Favorite';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => alert('Request failed.'));
        });
    });

    // Handle rating change (AJAX)
    const ratingSelect = document.getElementById('rating-select');
    if (ratingSelect) {
        ratingSelect.addEventListener('change', function() {
            const movieId = this.dataset.movie;
            const rating = this.value;
            if (rating === '') return;
            fetch('ajax/rate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `movie_id=${movieId}&rating=${rating}`
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) alert('Error: ' + data.message);
            })
            .catch(err => alert('Request failed.'));
        });
    }
});