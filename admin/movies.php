<?php
// admin/movies.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_movie = null;

// If editing, fetch movie data
if ($edit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_movie = $stmt->get_result()->fetch_assoc();
    if (!$edit_movie) {
        $edit_id = 0;
        $error = 'Movie not found.';
    }
}

// Handle form submission (Add or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $release_year = intval($_POST['release_year']);
    $duration = intval($_POST['duration']);
    $category_id = intval($_POST['category_id']);
    $poster = trim($_POST['poster_path']);
    $backdrop = trim($_POST['backdrop_path']);
    $video = trim($_POST['video_url']);
    $featured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($title) || empty($description)) {
        $error = 'Title and description are required.';
    } else {
        if ($_POST['action'] === 'add') {
            // Insert new movie
            $stmt = $conn->prepare("INSERT INTO movies (title, description, release_year, duration_minutes, poster_image, backdrop_image, video_url, category_id, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiisssii", $title, $description, $release_year, $duration, $poster, $backdrop, $video, $category_id, $featured);
            if ($stmt->execute()) {
                $message = 'Movie added successfully!';
            } else {
                $error = 'Error: ' . $conn->error;
            }
        } elseif ($_POST['action'] === 'update' && $edit_id > 0) {
            // Update existing movie
            $stmt = $conn->prepare("UPDATE movies SET title = ?, description = ?, release_year = ?, duration_minutes = ?, poster_image = ?, backdrop_image = ?, video_url = ?, category_id = ?, is_featured = ? WHERE id = ?");
            $stmt->bind_param("ssiisssiii", $title, $description, $release_year, $duration, $poster, $backdrop, $video, $category_id, $featured, $edit_id);
            if ($stmt->execute()) {
                $message = 'Movie updated successfully!';
                // Refresh edit data
                $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
                $stmt->bind_param("i", $edit_id);
                $stmt->execute();
                $edit_movie = $stmt->get_result()->fetch_assoc();
            } else {
                $error = 'Error: ' . $conn->error;
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = 'Movie deleted successfully!';
    } else {
        $error = 'Error deleting movie.';
    }
}

// Get all categories
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");

// Get all movies for listing
$movies = $conn->query("SELECT id, title, release_year, is_featured, created_at FROM movies ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Movies – Streamora Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #0a0a0a; }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid #333;
            margin-bottom: 2rem;
        }
        .admin-header h1 { color: #e50914; font-size: 1.8rem; }
        .admin-header .user-info { color: #aaa; }
        .admin-header .user-info a { color: #e50914; margin-left: 1rem; text-decoration: none; }
        .admin-nav {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .admin-nav a {
            background: #1a1a1a;
            color: #fff;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
        }
        .admin-nav a:hover,
        .admin-nav a.active { background: #e50914; }
        .message {
            background: #1a3a1a;
            color: #8cff8c;
            padding: 0.7rem;
            border-radius: 4px;
            margin: 1rem 0;
        }
        .error {
            background: #2a1212;
            color: #e50914;
            padding: 0.7rem;
            border-radius: 4px;
            margin: 1rem 0;
        }
        .admin-form {
            max-width: 600px;
            margin: 2rem 0;
            background: #1a1a1a;
            padding: 2rem;
            border-radius: 8px;
        }
        .admin-form .form-group {
            margin-bottom: 1rem;
        }
        .admin-form label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.3rem;
            color: #ccc;
        }
        .admin-form input,
        .admin-form textarea,
        .admin-form select {
            width: 100%;
            padding: 0.6rem;
            background: #222;
            color: #fff;
            border: 1px solid #444;
            border-radius: 4px;
        }
        .admin-form textarea { resize: vertical; }
        .admin-form .btn {
            padding: 0.7rem 2rem;
            background: #e50914;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .admin-form .btn:hover { background: #b20710; }
        .admin-form .btn-secondary {
            background: #333;
            margin-left: 0.5rem;
        }
        .admin-form .btn-secondary:hover { background: #555; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th { color: #aaa; font-weight: normal; }
        td { color: #fff; }
        td a {
            color: #e50914;
            text-decoration: none;
            margin-right: 0.5rem;
        }
        td a:hover { text-decoration: underline; }
        .badge-featured {
            background: #e50914;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
        }
        .movie-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2rem 0 1rem;
        }
        .movie-list-header h3 { color: #fff; }
        .toggle-form-btn {
            background: #333;
            color: #fff;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .toggle-form-btn:hover { background: #555; }
        .form-section { display: <?= ($edit_id > 0) ? 'block' : 'none' ?>; }
    </style>
    <script>
        function toggleForm() {
            const form = document.querySelector('.form-section');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>🎬 Streamora Admin</h1>
            <div class="user-info">
                Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="movies.php" class="active">Manage Movies</a>
            <a href="users.php">Users</a>
            <a href="../index.php" target="_blank">View Site</a>
        </div>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="movie-list-header">
            <h3>📽️ All Movies (<?= count($movies) ?>)</h3>
            <button class="toggle-form-btn" onclick="toggleForm()">
                <?= ($edit_id > 0) ? 'Edit Movie' : '+ Add New Movie' ?>
            </button>
        </div>

        <!-- Add/Edit Form -->
        <div class="form-section">
            <div class="admin-form">
                <h4 style="color: #fff; margin-bottom: 1rem;">
                    <?= ($edit_id > 0) ? '✏️ Edit Movie' : '➕ Add New Movie' ?>
                </h4>
                <form method="post">
                    <input type="hidden" name="action" value="<?= ($edit_id > 0) ? 'update' : 'add' ?>">
                    <?php if ($edit_id > 0): ?>
                        <input type="hidden" name="movie_id" value="<?= $edit_id ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($edit_movie['title'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" rows="4" required><?= htmlspecialchars($edit_movie['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Release Year</label>
                        <input type="number" name="release_year" value="<?= htmlspecialchars($edit_movie['release_year'] ?? date('Y')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration" value="<?= htmlspecialchars($edit_movie['duration_minutes'] ?? '') ?>" placeholder="120">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id">
                            <?php 
                            $categories->data_seek(0);
                            while ($cat = $categories->fetch_assoc()): 
                            ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($edit_movie['category_id']) && $edit_movie['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Poster Image Path</label>
                        <input type="text" name="poster_path" value="<?= htmlspecialchars($edit_movie['poster_image'] ?? '') ?>" placeholder="posters/yourmovie.jpg">
                        <small style="color: #666;">e.g., posters/salaar.jpg</small>
                    </div>
                    <div class="form-group">
                        <label>Backdrop Image Path</label>
                        <input type="text" name="backdrop_path" value="<?= htmlspecialchars($edit_movie['backdrop_image'] ?? '') ?>" placeholder="backdrops/yourmovie_bg.jpg">
                    </div>
                    <div class="form-group">
                        <label>Video URL</label>
                        <input type="text" name="video_url" value="<?= htmlspecialchars($edit_movie['video_url'] ?? '') ?>" placeholder="/videos/yourmovie.mp4">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_featured" value="1" <?= (isset($edit_movie['is_featured']) && $edit_movie['is_featured'] == 1) ? 'checked' : '' ?>>
                            Set as Featured (appears on homepage hero)
                        </label>
                    </div>
                    <div>
                        <button type="submit" class="btn"><?= ($edit_id > 0) ? 'Update Movie' : 'Add Movie' ?></button>
                        <?php if ($edit_id > 0): ?>
                            <a href="movies.php" class="btn btn-secondary" style="display: inline-block; padding: 0.7rem 2rem; background: #333; color: #fff; text-decoration: none; border-radius: 4px;">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Movie List -->
        <?php if (empty($movies)): ?>
            <p style="color: #666;">No movies added yet. Click "Add New Movie" to get started.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Year</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movies as $movie): ?>
                        <tr>
                            <td><?= $movie['id'] ?></td>
                            <td><?= htmlspecialchars($movie['title']) ?></td>
                            <td><?= $movie['release_year'] ?? 'N/A' ?></td>
                            <td><?= $movie['is_featured'] ? '<span class="badge-featured">★ Featured</span>' : '' ?></td>
                            <td>
                                <a href="movies.php?edit=<?= $movie['id'] ?>">✏️ Edit</a>
                                <a href="movies.php?delete=<?= $movie['id'] ?>" onclick="return confirm('Are you sure you want to delete this movie?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>