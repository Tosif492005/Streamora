<?php
// admin/users.php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch all users – compatible with all PHP setups
$users = [];
$result = $conn->query("SELECT id, username, email, is_admin, created_at FROM users ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle admin promotion/demotion
if (isset($_GET['toggle_admin']) && isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    if ($user_id == $_SESSION['admin_id']) {
        $message = 'You cannot change your own admin status.';
    } else {
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user) {
            $new_status = $user['is_admin'] ? 0 : 1;
            $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_status, $user_id);
            if ($stmt->execute()) {
                $message = "User admin status updated successfully.";
                // Refresh list
                $users = [];
                $result = $conn->query("SELECT id, username, email, is_admin, created_at FROM users ORDER BY created_at DESC");
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $users[] = $row;
                    }
                }
            } else {
                $error = "Error updating user.";
            }
        }
    }
}

// Handle delete user
if (isset($_GET['delete']) && isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    if ($user_id == $_SESSION['admin_id']) {
        $message = 'You cannot delete your own account.';
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "User deleted successfully.";
            // Refresh list
            $users = [];
            $result = $conn->query("SELECT id, username, email, admins, created_at FROM users ORDER BY created_at DESC");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $users[] = $row;
                }
            }
        } else {
            $error = "Error deleting user.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users – Streamora Admin</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.7rem;
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
        .badge-admin {
            background: #e50914;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
        }
        .badge-user {
            background: #333;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
        }
    </style>
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
            <a href="movies.php">Manage Movies</a>
            <a href="users.php" class="active">Users</a>
            <a href="../index.php" target="_blank">View Site</a>
        </div>

        <?php if (isset($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <h3 style="color: #fff; margin: 1rem 0;">All Users (<?= count($users) ?>)</h3>
        <?php if (empty($users)): ?>
            <p style="color: #666;">No users found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge-admin">Admin</span>
                                <?php else: ?>
                                    <span class="badge-user">User</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                    <a href="users.php?toggle_admin=1&user_id=<?= $user['id'] ?>">
                                        <?= $user['is_admin'] ? 'Remove Admin' : 'Make Admin' ?>
                                    </a>
                                    <a href="users.php?delete=1&user_id=<?= $user['id'] ?>" onclick="return confirm('Delete user <?= htmlspecialchars($user['username']) ?>?')">Delete</a>
                                <?php else: ?>
                                    <span style="color: #666;">(You)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>