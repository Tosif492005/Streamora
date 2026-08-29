<?php
// admin/dashboard.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get stats
$total_movies = $conn->query("SELECT COUNT(*) as count FROM movies")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$total_featured = $conn->query("SELECT COUNT(*) as count FROM movies WHERE is_featured = 1")->fetch_assoc()['count'];

// Get recent movies
$recent_movies = $conn->query("SELECT id, title, release_year, created_at FROM movies ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – Streamora</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Admin Layout */
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
        .admin-header h1 {
            color: #e50914;
            font-size: 1.8rem;
        }
        .admin-header .user-info {
            color: #aaa;
        }
        .admin-header .user-info a {
            color: #e50914;
            margin-left: 1rem;
            text-decoration: none;
        }
        .admin-header .user-info a:hover {
            text-decoration: underline;
        }
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
            transition: background 0.3s;
        }
        .admin-nav a:hover,
        .admin-nav a.active {
            background: #e50914;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #1a1a1a;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #e50914;
        }
        .stat-card .label {
            color: #aaa;
            margin-top: 0.3rem;
        }
        .recent-movies {
            background: #1a1a1a;
            padding: 1.5rem;
            border-radius: 8px;
        }
        .recent-movies h3 {
            margin-bottom: 1rem;
            color: #fff;
        }
        .recent-movies table {
            width: 100%;
            border-collapse: collapse;
        }
        .recent-movies th,
        .recent-movies td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        .recent-movies th {
            color: #aaa;
            font-weight: normal;
        }
        .recent-movies td {
            color: #fff;
        }
        .recent-movies a {
            color: #e50914;
            text-decoration: none;
        }
        .recent-movies a:hover {
            text-decoration: underline;
        }
        .logout-btn {
            background: #333;
            padding: 0.3rem 1rem;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
        }
        .logout-btn:hover {
            background: #e50914;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>🎬 Streamora Admin</h1>
            <div class="user-info">
                Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="admin-nav">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="movies.php">Manage Movies</a>
            <a href="users.php">Users</a>
            <a href="../index.php" target="_blank">View Site</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?= $total_movies ?></div>
                <div class="label">Total Movies</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $total_users ?></div>
                <div class="label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $total_categories ?></div>
                <div class="label">Categories</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $total_featured ?></div>
                <div class="label">Featured Movies</div>
            </div>
        </div>

        <div class="recent-movies">
            <h3>📽️ Recently Added Movies</h3>
            <?php if (empty($recent_movies)): ?>
                <p style="color: #666;">No movies added yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Added</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_movies as $movie): ?>
                            <tr>
                                <td><?= $movie['id'] ?></td>
                                <td><?= htmlspecialchars($movie['title']) ?></td>
                                <td><?= $movie['release_year'] ?? 'N/A' ?></td>
                                <td><?= date('M d, Y', strtotime($movie['created_at'])) ?></td>
                                <td><a href="movies.php?edit=<?= $movie['id'] ?>">Edit</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>